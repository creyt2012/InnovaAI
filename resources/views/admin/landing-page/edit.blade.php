<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Quản lý Landing Page</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('admin.landing-page.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Hero Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Hero Section</h3>
                        
                        <div class="mb-4">
                            <label class="block mb-2">Tiêu đề</label>
                            <input type="text" 
                                   name="hero_title" 
                                   value="{{ old('hero_title', $landingPage->hero_title) }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Mô tả</label>
                            <textarea name="hero_description" 
                                      rows="3"
                                      class="w-full border rounded p-2">{{ old('hero_description', $landingPage->hero_description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Hero Image</label>
                            @if($landingPage->hero_image)
                                <img src="{{ $landingPage->hero_image }}" 
                                     alt="Hero" 
                                     class="h-32 mb-2">
                            @endif
                            <input type="file" 
                                   name="hero_image" 
                                   accept="image/*"
                                   class="border p-2">
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Tính năng</h3>
                        
                        <div id="features-container">
                            @foreach(old('features', $landingPage->features ?? []) as $index => $feature)
                            <div class="feature-item mb-4 p-4 border rounded">
                                <div class="mb-2">
                                    <label class="block mb-1">Icon</label>
                                    <input type="text" 
                                           name="features[{{ $index }}][icon]"
                                           value="{{ $feature['icon'] }}"
                                           class="w-full border rounded p-2">
                                </div>
                                <div class="mb-2">
                                    <label class="block mb-1">Tiêu đề</label>
                                    <input type="text" 
                                           name="features[{{ $index }}][title]"
                                           value="{{ $feature['title'] }}"
                                           class="w-full border rounded p-2">
                                </div>
                                <div class="mb-2">
                                    <label class="block mb-1">Mô tả</label>
                                    <textarea name="features[{{ $index }}][description]"
                                              class="w-full border rounded p-2">{{ $feature['description'] }}</textarea>
                                </div>
                                <button type="button" 
                                        onclick="removeFeature(this)"
                                        class="text-red-600">
                                    Xóa
                                </button>
                            </div>
                            @endforeach
                        </div>

                        <button type="button" 
                                onclick="addFeature()"
                                class="bg-blue-500 text-white px-4 py-2 rounded">
                            Thêm tính năng
                        </button>
                    </div>

                    <!-- SEO Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">SEO</h3>
                        
                        <div class="mb-4">
                            <label class="block mb-2">Meta Title</label>
                            <input type="text" 
                                   name="meta_title" 
                                   value="{{ old('meta_title', $landingPage->meta_title) }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Meta Description</label>
                            <textarea name="meta_description" 
                                      rows="3"
                                      class="w-full border rounded p-2">{{ old('meta_description', $landingPage->meta_description) }}</textarea>
                        </div>
                    </div>

                    <!-- Custom Code -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Custom Code</h3>
                        
                        <div class="mb-4">
                            <label class="block mb-2">Custom CSS</label>
                            <textarea name="custom_css" 
                                      rows="5"
                                      class="w-full border rounded p-2 font-mono">{{ old('custom_css', $landingPage->custom_css) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Custom JavaScript</label>
                            <textarea name="custom_js" 
                                      rows="5"
                                      class="w-full border rounded p-2 font-mono">{{ old('custom_js', $landingPage->custom_js) }}</textarea>
                        </div>
                    </div>

                    <button type="submit" 
                            class="bg-green-500 text-white px-6 py-2 rounded">
                        Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function addFeature() {
        const container = document.getElementById('features-container');
        const index = container.children.length;
        
        const template = `
            <div class="feature-item mb-4 p-4 border rounded">
                <div class="mb-2">
                    <label class="block mb-1">Icon</label>
                    <input type="text" 
                           name="features[${index}][icon]"
                           class="w-full border rounded p-2">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Tiêu đề</label>
                    <input type="text" 
                           name="features[${index}][title]"
                           class="w-full border rounded p-2">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Mô tả</label>
                    <textarea name="features[${index}][description]"
                              class="w-full border rounded p-2"></textarea>
                </div>
                <button type="button" 
                        onclick="removeFeature(this)"
                        class="text-red-600">
                    Xóa
                </button>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', template);
    }

    function removeFeature(button) {
        button.closest('.feature-item').remove();
    }
    </script>
</x-app-layout> 