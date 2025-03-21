package com.company.app.config

import org.springframework.context.annotation.Configuration
import org.springframework.context.event.EventListener
import org.springframework.scheduling.annotation.EnableAsync
import org.springframework.scheduling.annotation.Async

@Configuration
@EnableAsync
class EventConfig {
    
    @Async
    @EventListener
    fun handleAsyncEvent(event: Any) {
        // Xử lý event
    }
} 