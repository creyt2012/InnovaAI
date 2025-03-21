package com.company.app.config

import org.springframework.boot.context.event.ApplicationReadyEvent
import org.springframework.context.annotation.Configuration
import org.springframework.context.event.EventListener

@Configuration
class ApplicationBootstrap {

    @EventListener(ApplicationReadyEvent::class)
    fun onApplicationEvent() {
        // Khởi tạo các thành phần cần thiết khi ứng dụng khởi động
    }
} 