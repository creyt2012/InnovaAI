<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Quản lý Node LM Studio</h2>
            <a href="{{ route('admin.lmstudio.nodes.create') }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                Thêm Node Mới
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b">Tên</th>
                            <th class="px-6 py-3 border-b">URL</th>
                            <th class="px-6 py-3 border-b">Trạng thái</th>
                            <th class="px-6 py-3 border-b">Độ ưu tiên</th>
                            <th class="px-6 py-3 border-b">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nodes as $node)
                        <tr>
                            <td class="px-6 py-4">{{ $node->name }}</td>
                            <td class="px-6 py-4">{{ $node->url }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-sm {{ $node->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $node->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $node->priority }}</td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button onclick="testNode({{ $node->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    <a href="{{ route('admin.lmstudio.nodes.edit', $node) }}" 
                                       class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.lmstudio.nodes.destroy', $node) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa node này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function testNode(nodeId) {
        fetch(`/admin/lmstudio/nodes/${nodeId}/test`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi kiểm tra node');
        });
    }
    </script>
</x-app-layout> 