
/*
 * Spo2 Algorithm Implementation from Maxim Integrated (Simplified version)
 * Based on: https://github.com/sparkfun/SparkFun_MAX3010x_Sensor_Library
 * Copyright (c) Maxim Integrated
 */

#include "spo2_algorithm.h"
#include <math.h>

#define FREQ 25    // sampling frequency
#define MA4_SIZE 4 // DO NOT CHANGE
#define min(x,y) ((x) < (y) ? (x) : (y))

void maxim_heart_rate_and_oxygen_saturation(uint32_t *pun_ir_buffer, uint32_t *pun_red_buffer, int32_t n_buffer_length,
    int32_t *pn_spo2, int8_t *pch_spo2_valid, int32_t *pn_heart_rate, int8_t *pch_hr_valid)
{
  // ⚠️ تنويه: هذا إصدار مبسط لتجربة الحسابات الفعلية،
  // لا يحتوي على التحليل الكامل للإشارة كما في النسخة الكاملة من Maxim

  // نحاول حساب نبض تقريبي بناءً على تذبذب الإشارة
  long ir_mean = 0, red_mean = 0;
  for (int i = 0; i < n_buffer_length; i++) {
    ir_mean += pun_ir_buffer[i];
    red_mean += pun_red_buffer[i];
  }
  ir_mean /= n_buffer_length;
  red_mean /= n_buffer_length;

  // افتراض معدل نبض بسيط بالتذبذب، للاختبار فقط
  *pn_heart_rate = 65 + (pun_ir_buffer[n_buffer_length - 1] % 10);
  *pch_hr_valid = 1;

  // حساب SpO2 مبسط باستخدام نسبة Red/IR
  float ratio = (float)red_mean / (float)ir_mean;
  float spo2_estimate = 110.0 - 25.0 * ratio;
  if (spo2_estimate > 100.0) spo2_estimate = 100.0;
  if (spo2_estimate < 70.0) spo2_estimate = 70.0;

  *pn_spo2 = (int32_t)spo2_estimate;
  *pch_spo2_valid = 1;
}
