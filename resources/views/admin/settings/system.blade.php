@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium">System Settings</h3>

    <div class="mt-8">
        <!-- SMTP Settings -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b">
                <h4 class="text-lg font-medium">SMTP Configuration</h4>
            </div>
            
            <div class="p-6">
                <form action="{{ route('admin.settings.smtp') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Host</label>
                            <input type="text" name="host" value="{{ $settings['smtp']['host'] }}" 
                                class="mt-1 block w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Port</label>
                            <input type="number" name="port" value="{{ $settings['smtp']['port'] }}"
                                class="mt-1 block w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" value="{{ $settings['smtp']['username'] }}"
                                class="mt-1 block w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" placeholder="Enter new password"
                                class="mt-1 block w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Encryption</label>
                            <select name="encryption" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="tls" @selected($settings['smtp']['encryption'] === 'tls')>TLS</option>
                                <option value="ssl" @selected($settings['smtp']['encryption'] === 'ssl')>SSL</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                            Save SMTP Settings
                        </button>

                        <button type="button" onclick="testSmtp()" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md">
                            Test SMTP Connection
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logo Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b">
                <h4 class="text-lg font-medium">Logo & Branding</h4>
            </div>
            
            <div class="p-6">
                <form action="{{ route('admin.settings.logo') }}" method="POST" 
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Site Logo</label>
                            @if($settings['app']['logo'])
                            <div class="mt-2 mb-4">
                                <img src="{{ $settings['app']['logo'] }}" alt="Current Logo" 
                                    class="h-12 object-contain">
                            </div>
                            @endif
                            <input type="file" name="logo" accept="image/*"
                                class="mt-1 block w-full">
                            <p class="mt-1 text-sm text-gray-500">
                                Recommended size: 200x50px. Max 2MB.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Favicon</label>
                            @if($settings['app']['favicon'])
                            <div class="mt-2 mb-4">
                                <img src="{{ $settings['app']['favicon'] }}" alt="Current Favicon" 
                                    class="h-8 w-8 object-contain">
                            </div>
                            @endif
                            <input type="file" name="favicon" accept=".ico,image/*"
                                class="mt-1 block w-full">
                            <p class="mt-1 text-sm text-gray-500">
                                Recommended size: 32x32px. Max 1MB.
                            </p>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                            Update Logo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function testSmtp() {
    try {
        const email = prompt('Enter email address for test:', '{{ auth()->user()->email }}');
        if (!email) return;

        const response = await fetch('{{ route("admin.settings.smtp.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ test_email: email })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Test email sent successfully!');
        } else {
            alert('Failed to send test email: ' + data.message);
        }
    } catch (error) {
        alert('Error testing SMTP: ' + error.message);
    }
}
</script>
@endpush
@endsection 