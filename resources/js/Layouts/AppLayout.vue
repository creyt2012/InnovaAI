<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
          </div>
          
          <div class="flex items-center">
            <LanguageSwitcher :current-locale="$page.props.locale" />
            <!-- Other nav items -->
          </div>
        </div>
      </div>
    </nav>

    <main>
      <slot />
    </main>
  </div>
</template> 