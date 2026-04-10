@extends('layouts.app')

@section('title', 'Nouveau colis')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Nouveau colis</h1>
        <p class="text-sm text-gray-500">Remplissez les informations pour déclarer un nouveau colis</p>
    </div>

    <form action="{{ route('parcels.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="transport_mode">Mode de transport</label>
                <select id="transport_mode" name="transport_mode" class="input-select">
                    <option value="air_normal">Avion (standard)</option>
                    <option value="air_express">Avion (express)</option>
                    <option value="sea">Mer</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="origin_country">Pays d'origine</label>
                <input id="origin_country" name="origin_country" type="text" class="input-text" required>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="destination_country">Pays de destination</label>
                <input id="destination_country" name="destination_country" type="text" class="input-text" required>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="declared_value">Valeur déclarée (€)</label>
                <input id="declared_value" name="declared_value" type="number" min="0" step="0.01" class="input-text">
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700" for="content_description">Description du contenu</label>
            <textarea id="content_description" name="content_description" rows="3" class="input-text"></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Enregistrer
            </button>
        </div>
    </form>

    <div id="keywordModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/40" data-modal-close></div>
        <div class="relative mx-auto mt-24 w-[92%] max-w-lg rounded-2xl bg-white border border-gray-200 shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <div id="keywordModalTitle" class="text-sm font-semibold text-gray-900">Alerte</div>
            </div>
            <div class="p-6">
                <div id="keywordModalMessage" class="text-sm text-gray-700"></div>
                <div class="mt-5 flex items-center justify-end gap-2">
                    <button type="button" class="btn-outline-primary rounded-xl px-4 py-2" data-modal-close>Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.querySelector('form[action="{{ route('parcels.store') }}"]');
            const desc = document.getElementById('content_description');
            const modal = document.getElementById('keywordModal');
            const modalTitle = document.getElementById('keywordModalTitle');
            const modalMsg = document.getElementById('keywordModalMessage');

            if (!form || !desc || !modal || !modalTitle || !modalMsg) return;

            let forbidden = [];
            let known = [];
            let hasForbidden = false;

            function openModal(title, message) {
                modalTitle.textContent = title;
                modalMsg.textContent = message;
                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
            }

            modal.querySelectorAll('[data-modal-close]').forEach((el) => {
                el.addEventListener('click', closeModal);
            });

            function normalizeText(text) {
                return (text || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/\p{Diacritic}/gu, ' ')
                    .replace(/[^a-z0-9\s]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            function containsWholeWord(haystack, needle) {
                const escaped = needle.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const re = new RegExp(`(^|\\s)${escaped}(\\s|$)`, 'i');
                return re.test(haystack);
            }

            function checkDescription() {
                const text = normalizeText(desc.value);
                hasForbidden = false;

                if (!text) return;

                const foundForbidden = forbidden.find((kw) => containsWholeWord(text, kw));
                if (foundForbidden) {
                    hasForbidden = true;
                    openModal('Colis non accepté', `Le contenu contient un mot interdit: "${foundForbidden}".`);
                    return;
                }

                const foundKnown = known.find((kw) => containsWholeWord(text, kw));
                if (!foundKnown) {
                    openModal('Article inconnu', "Le système ne reconnaît pas ce type d'article. Vérifie la description.");
                }
            }

            fetch("{{ route('product-keywords.json') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then((r) => r.json())
                .then((data) => {
                    forbidden = (data.forbidden || []).map(normalizeText).filter(Boolean);
                    known = (data.known || []).map(normalizeText).filter(Boolean);
                })
                .catch(() => {
                    forbidden = [];
                    known = [];
                });

            let debounce;
            desc.addEventListener('input', () => {
                clearTimeout(debounce);
                debounce = setTimeout(checkDescription, 300);
            });

            form.addEventListener('submit', (e) => {
                checkDescription();
                if (hasForbidden) {
                    e.preventDefault();
                }
            });
        })();
    </script>
</div>
@endsection
