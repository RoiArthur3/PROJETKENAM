<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" value="Téléphone" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" placeholder="+225 01 02 03 04 05" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            <p class="text-xs text-gray-600 mt-1">Format: +225 01 02 03 04 05 ou 01 02 03 04 05</p>
        </div>

        <!-- Service -->
        <div class="mt-4">
            <x-input-label for="service_id" value="Service" />
            <select id="service_id" name="service_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">Sélectionner un service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->nom }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label value="Rôle" />
            <div class="mt-2 space-y-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="agent" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('role', 'agent') == 'agent' ? 'checked' : '' }} required>
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>Agent</strong> - Accès aux opérations et requêtes
                    </span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="moderator" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('role') == 'moderator' ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>Modérateur</strong> - Choix des modules d'accès
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Modules (visible only for Moderator) -->
        <div class="mt-4" id="modulesSection" style="display: none;">
            <x-input-label value="Modules d'accès" />
            <p class="text-xs text-gray-600 mb-2">Sélectionnez les modules auxquels le modérateur aura accès</p>
            <div class="mt-2 space-y-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                @foreach($allModules as $key => $module)
                    <label class="flex items-center">
                        <input type="checkbox" name="modules[]" value="{{ $key }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array($key, old('modules', [])) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="{{ $module['icon'] }} mr-1"></i>
                            {{ $module['label'] }}
                        </span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('modules')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        // Afficher/masquer la section modules selon le rôle sélectionné
        document.addEventListener('DOMContentLoaded', function() {
            const roleInputs = document.querySelectorAll('input[name="role"]');
            const modulesSection = document.getElementById('modulesSection');

            function toggleModulesSection() {
                const selectedRole = document.querySelector('input[name="role"]:checked').value;
                if (selectedRole === 'moderator') {
                    modulesSection.style.display = 'block';
                } else {
                    modulesSection.style.display = 'none';
                    // Décocher tous les modules si on passe à Agent
                    document.querySelectorAll('input[name="modules[]"]').forEach(cb => cb.checked = false);
                }
            }

            // Écouter les changements de rôle
            roleInputs.forEach(input => {
                input.addEventListener('change', toggleModulesSection);
            });

            // Initialiser l'affichage au chargement
            toggleModulesSection();
        });
    </script>
</x-guest-layout>
