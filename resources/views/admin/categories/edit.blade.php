{{-- resources/views/admin/categories/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.categories.index') }}"
                class="mr-4 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Category') }} - {{ $category->name }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Usage Warning -->
                @if ($category->transactions_count > 0)
                    <div
                        class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                        <div class="flex">
                            <i
                                class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mr-3 mt-1 flex-shrink-0"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1">Category In
                                    Use</h4>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                    This category is currently used by {{ $category->transactions_count }}
                                    transaction(s). Changes will affect all associated transactions.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.categories.update', $category) }}" x-data="categoryForm()">
                    @csrf
                    @method('PATCH')

                    <!-- Preview Card -->
                    <div
                        class="mb-6 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Preview</h4>
                        <div class="flex items-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl transition-all"
                                :style="{ backgroundColor: color + '20', color: color }">
                                <span x-text="icon"></span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="name"></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400"
                                    x-text="description || 'No description'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Category Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                            x-model="name"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" x-model="description"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icon -->
                    <div class="mb-6">
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Icon (Emoji) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}"
                            x-model="icon"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                            maxlength="10" required>

                        <!-- Icon Suggestions -->
                        <div class="mt-3 grid grid-cols-8 sm:grid-cols-12 gap-2">
                            <button type="button" @click="icon = '🍔'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">🍔</button>
                            <button type="button" @click="icon = '🚗'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">🚗</button>
                            <button type="button" @click="icon = '🏠'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">🏠</button>
                            <button type="button" @click="icon = '🛒'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">🛒</button>
                            <button type="button" @click="icon = '🎬'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">🎬</button>
                            <button type="button" @click="icon = '💊'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">💊</button>
                            <button type="button" @click="icon = '📚'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">📚</button>
                            <button type="button" @click="icon = '💡'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">💡</button>
                            <button type="button" @click="icon = '💰'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">💰</button>
                            <button type="button" @click="icon = '💼'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">💼</button>
                            <button type="button" @click="icon = '🎁'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">🎁</button>
                            <button type="button" @click="icon = '✈️'"
                                class="p-2 text-2xl hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">✈️</button>
                        </div>

                        @error('icon')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div class="mb-6">
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="color" name="color" id="color"
                                value="{{ old('color', $category->color) }}" x-model="color"
                                class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer"
                                required>
                            <input type="text" x-model="color"
                                class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <!-- Color Presets -->
                        <div class="mt-3 grid grid-cols-8 sm:grid-cols-12 gap-2">
                            <button type="button" @click="color = '#EF4444'" class="w-full h-8 rounded"
                                style="background-color: #EF4444"></button>
                            <button type="button" @click="color = '#F59E0B'" class="w-full h-8 rounded"
                                style="background-color: #F59E0B"></button>
                            <button type="button" @click="color = '#84CC16'" class="w-full h-8 rounded"
                                style="background-color: #84CC16"></button>
                            <button type="button" @click="color = '#10B981'" class="w-full h-8 rounded"
                                style="background-color: #10B981"></button>
                            <button type="button" @click="color = '#06B6D4'" class="w-full h-8 rounded"
                                style="background-color: #06B6D4"></button>
                            <button type="button" @click="color = '#3B82F6'" class="w-full h-8 rounded"
                                style="background-color: #3B82F6"></button>
                            <button type="button" @click="color = '#8B5CF6'" class="w-full h-8 rounded"
                                style="background-color: #8B5CF6"></button>
                            <button type="button" @click="color = '#EC4899'" class="w-full h-8 rounded"
                                style="background-color: #EC4899"></button>
                            <button type="button" @click="color = '#6B7280'" class="w-full h-8 rounded"
                                style="background-color: #6B7280"></button>
                            <button type="button" @click="color = '#DC2626'" class="w-full h-8 rounded"
                                style="background-color: #DC2626"></button>
                            <button type="button" @click="color = '#7C3AED'" class="w-full h-8 rounded"
                                style="background-color: #7C3AED"></button>
                            <button type="button" @click="color = '#059669'" class="w-full h-8 rounded"
                                style="background-color: #059669"></button>
                        </div>

                        @error('color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('admin.categories.index') }}"
                            class="mb-3 sm:mb-0 inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <div class="flex space-x-2">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>
                                Update Category
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function categoryForm() {
            return {
                name: '{{ old('name', $category->name) }}',
                description: '{{ old('description', $category->description) }}',
                icon: '{{ old('icon', $category->icon) }}',
                color: '{{ old('color', $category->color) }}'
            }
        }
    </script>
</x-app-layout>
