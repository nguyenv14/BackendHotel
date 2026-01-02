<?php

namespace App\Http\Enums;

class OrderStatus
{
    const WAITING_FOR_APPROVAL = 0;      // Đang chờ duyệt (hoặc đã duyệt, chưa check-in)
    const CHECK_IN = 1;                  // Đang đợi check-in
    const CHECK_OUT = 2;                 // Đã checkin và chờ check-out
    const COMPLETED = 3;                 // Đã hoàn thành (sau khi đánh giá)
    const NO_SHOW = 4;                   // No show - quá thời gian check-in
    const CANCELLED_BY_ADMIN = -1;       // Đã hủy bởi admin
    const CANCELLED_BY_CUSTOMER = -2;    // Đã hủy bởi khách hàng
}