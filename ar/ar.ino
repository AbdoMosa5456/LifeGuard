#include <Wire.h>
#include "MAX30105.h"
#include "spo2_algorithm.h"
#include <SoftwareSerial.h>

MAX30105 sensor;
SoftwareSerial sim808(10, 11); // TX, RX

const int lm35Pin = A0;
const float lm35Threshold = 30.0;
const byte bufferLength = 100;

uint32_t irBuffer[bufferLength];
uint32_t redBuffer[bufferLength];

int32_t spo2;
int8_t validSpO2;
int32_t heartRate;
int8_t validHeartRate;

const char apn[] = "internet.vodafone.net"; // APN لشريحة فودافون مصر
const char api_url[] = "http://localhost/h/api.php"; // رابط الـ API المحلي

void setup() {
  Serial.begin(115200);
  sim808.begin(9600); // تم التعديل لتناسب أغلب وحدات SIM808

  if (!sensor.begin(Wire, I2C_SPEED_STANDARD)) {
    Serial.println("MAX30102 not detected.");
    while (1);
  }

  sensor.setup(255, 16, 2, 400, 69, 16384); // إعداد دقيق
  delay(2000);
  
  connectToGPRS(); // الاتصال بـ GPRS
}

void loop() {
  Serial.println("Place your finger on MAX30102 sensor...");
  delay(1000); // تقليل زمن التوقف

  int totalHR = 0;
  int totalSpO2 = 0;
  int count = 0;

  for (int j = 0; j < 4; j++) {
    delay(1000);  // wait 1 second before reading
    byte i = 0;
    bool gotValidReading = false;

    while (!gotValidReading) {
      i = 0;

      while (i < bufferLength) {
        sensor.check();

        if (sensor.available()) {
          uint32_t irValue = sensor.getIR();
          if (irValue < 15000) {
            sensor.nextSample();
            delay(100);
            continue;
          }

          redBuffer[i] = sensor.getRed();
          irBuffer[i] = irValue;
          sensor.nextSample();
          i++;
        }
      }

      maxim_heart_rate_and_oxygen_saturation(irBuffer, redBuffer, bufferLength,
                                              &spo2, &validSpO2, &heartRate, &validHeartRate);

      if (validHeartRate && validSpO2) {
        gotValidReading = true;
        Serial.print("❤️ Heart Rate: ");
        Serial.print(heartRate);
        Serial.println(" bpm");
        delay(1000);
        
        Serial.print("🫧 SpO2: ");
        Serial.print(spo2);
        Serial.println(" %");
        delay(1000);

        totalHR += heartRate;
        totalSpO2 += spo2;
        count++;

        // إرسال البيانات إلى API المحلي
        sendToAPI(heartRate, spo2, readTemperature());
      } else {
        Serial.println("Unstable reading, retrying...");
        delay(1000);
      }
    }

    delay(1000); // 1 second delay between readings
  }

  if (count > 0) {
    Serial.print("🧮 Average Heart Rate: ");
    Serial.print(totalHR / count);
    Serial.print(" bpm | ");
    Serial.print("🧮 Average SpO2: ");
    Serial.print(totalSpO2 / count);
    Serial.println(" %");
  }

  // قراءة درجة الحرارة من مستشعر LM35
  Serial.println("Now touch the LM35 sensor...");
  delay(5000);  // انتظار 5 ثوانٍ بعد عرض الرسالة

  float temp = readTemperature();

  if (temp >= lm35Threshold) {
    Serial.print("🌡️ Temperature: ");
    Serial.print(temp);
    Serial.println(" °C");
  } else {
    Serial.println("No finger detected on LM35.");
  }

  delay(2000); // مهلة بسيطة قبل تكرار الدورة
}

// دالة قراءة درجة الحرارة من LM35
float readTemperature() {
  float temp = analogRead(lm35Pin) * (5.0 / 1023.0) * 100;
  return temp;
}

// دالة الاتصال بالـ GPRS
void connectToGPRS() {
  sendAT("AT");
  sendAT("AT+SAPBR=3,1,\"Contype\",\"GPRS\"");
  sendAT("AT+SAPBR=3,1,\"APN\",\"" + String(apn) + "\"");
  sendAT("AT+SAPBR=1,1");
  sendAT("AT+SAPBR=2,1");
}

// دالة إرسال البيانات إلى API المحلي (php)
void sendToAPI(int hr, int spo2, float temp) {
  String localUrl = String(api_url) + "?heart_rate=" + String(hr) + "&spo2=" + String(spo2) + "&temp=" + String(temp, 1);
  Serial.print("Sending to Local API: ");
  Serial.println(localUrl);
  sendAT("AT+HTTPTERM");
  sendAT("AT+HTTPINIT");
  sendAT("AT+HTTPPARA=\"CID\",1");
  sendAT("AT+HTTPPARA=\"URL\",\"" + localUrl + "\"");
  sendAT("AT+HTTPACTION=0");
  delay(6000);
  sendAT("AT+HTTPREAD");
  sendAT("AT+HTTPTERM");
}

// دالة إرسال الأوامر إلى وحدة GSM
void sendAT(String cmd) {
  sim808.println(cmd);
  delay(500);  // تأخير نصف ثانية بعد إرسال الأمر
  waitResponse();
}

// دالة الانتظار لانتظار الاستجابة من وحدة GSM
void waitResponse() {
  unsigned long timeout = millis() + 5000;
  while (millis() < timeout) {
    while (sim808.available()) {
      char c = sim808.read();
      Serial.write(c);  // طباعة الردود من وحدة SIM808
    }
  }
}
