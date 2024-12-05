<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('サポートプロフィール編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- 成功メッセージ -->
                    @if (session('status'))
                        <div class="mb-4 text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- エラーメッセージ -->
                    @if ($errors->any())
                        <div class="mb-4 text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- フォーム -->
                        <form method="POST" action="{{ route('supporter-profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <!-- 認証画像 -->
                            <div class="mb-4">
                                <x-input-label for="pref_photo" :value="__('認証画像')" />
                                <input id="pref_photo" name="pref_photo" type="file" class="block mt-1 w-full" />
                                                           <!-- 現在の画像を表示 -->
                            @if ($profile && $profile->pref_photo)
                            <div class="mt-4">
                                <p>現在の画像:</p>
                                <img src="{{ asset('storage/' . $profile->pref_photo) }}" alt="現在の画像" class="w-48">
                            </div>
                        @endif
                            </div>

                    <!-- フォーム -->
                    <form method="POST" action="{{ route('supporter-profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <!-- 自己紹介 -->
                        <div class="mb-4">
                            <x-input-label for="self_introduction" :value="__('自己紹介')" />
                            <textarea id="self_introduction" name="self_introduction" class="block mt-1 w-full">{{ old('self_introduction', $profile->self_introduction ?? '') }}</textarea>
                        </div>

                        <!-- スキル 1 -->
                        <div class="mb-4">
                            <x-input-label for="skill1" :value="__('スキル 1')" />
                            <x-text-input id="skill1" name="skill1" type="text" class="mt-1 block w-full" :value="old('skill1', $profile->skill1 ?? '')" />
                        </div>

                        <!-- スキル 2 -->
                        <div class="mb-4">
                            <x-input-label for="skill2" :value="__('スキル 2')" />
                            <x-text-input id="skill2" name="skill2" type="text" class="mt-1 block w-full" :value="old('skill2', $profile->skill2 ?? '')" />
                        </div>

                        <!-- スキル 3 -->
                        <div class="mb-4">
                            <x-input-label for="skill3" :value="__('スキル 3')" />
                            <x-text-input id="skill3" name="skill3" type="text" class="mt-1 block w-full" :value="old('skill3', $profile->skill3 ?? '')" />
                        </div>

                        <!-- スキル 4 -->
                        <div class="mb-4">
                            <x-input-label for="skill4" :value="__('スキル 4')" />
                            <x-text-input id="skill4" name="skill4" type="text" class="mt-1 block w-full" :value="old('skill4', $profile->skill4 ?? '')" />
                        </div>

                        <!-- スキル 5 -->
                        <div class="mb-4">
                            <x-input-label for="skill5" :value="__('スキル 5')" />
                            <x-text-input id="skill5" name="skill5" type="text" class="mt-1 block w-full" :value="old('skill5', $profile->skill5 ?? '')" />
                        </div>
     <!-- 保存ボタン -->
     <div class="flex items-center justify-end mt-4">
        <x-primary-button class="ml-4">
            {{ __('保存') }}
        </x-primary-button>
    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('preview');
            reader.onload = () => {
                preview.src = reader.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</x-app-layout>
