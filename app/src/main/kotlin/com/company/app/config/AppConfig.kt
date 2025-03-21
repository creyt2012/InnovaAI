package com.company.app.config

import org.springframework.context.annotation.Configuration
import org.springframework.context.annotation.Bean
import org.springframework.boot.web.servlet.ServletRegistrationBean
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer

@Configuration
class AppConfig : WebMvcConfigurer {
    // Cấu hình chung của ứng dụng
    @Bean
    fun init() {
        // Khởi tạo các service cần thiết
    }
} 