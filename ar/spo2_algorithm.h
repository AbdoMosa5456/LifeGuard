
#ifndef SPO2_ALGORITHM_H_
#define SPO2_ALGORITHM_H_

#include <stdint.h>

void maxim_heart_rate_and_oxygen_saturation(uint32_t *pun_ir_buffer, uint32_t *pun_red_buffer, int32_t n_buffer_length,
    int32_t *pn_spo2, int8_t *pch_spo2_valid, int32_t *pn_heart_rate, int8_t *pch_hr_valid);

#endif
