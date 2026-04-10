@extends('layouts.app')

@section('title', 'Envoyer un colis')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Envoyer un colis</h1>
                <p class="mt-1 text-sm text-blue-700">Déclarez votre colis et suivez son acheminement par avion ou par bateau en toute simplicité.</p>
            </div>
            <div class="flex items-center gap-2">
                            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <div class="text-xs font-semibold text-blue-700">Étape 1</div>
                <div class="text-sm font-medium text-gray-900">Colis</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <div class="text-xs font-semibold text-blue-700">Étape 2</div>
                <div class="text-sm font-medium text-gray-900">Poids & dimensions</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <div class="text-xs font-semibold text-blue-700">Étape 3</div>
                <div class="text-sm font-medium text-gray-900">Origine & destination</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <div class="text-xs font-semibold text-blue-700">Étape 4</div>
                <div class="text-sm font-medium text-gray-900">Documents</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <div class="text-xs font-semibold text-blue-700">Étape 5</div>
                <div class="text-sm font-medium text-gray-900">Résumé</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">

    <!-- Étape 1 - Informations du colis -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Étape 1 – Informations du colis</h2>
            <p class="mt-1 text-sm text-blue-700">Choisis le mode d’envoi et décris le contenu.</p>
        </div>
        <div class="p-6">
            <form id="shipmentForm" method="POST" action="{{ route('shipments.store') }}" enctype="multipart/form-data">
                @csrf
                <!-- Type d'envoi -->
                <div class="space-y-2 mb-4">
                    <label class="block text-sm font-medium text-gray-700">Mode de transport</label>
                    <div class="flex flex-wrap items-center gap-4">
                        <label class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900">
                            <input type="radio" name="transport_mode" value="air_normal" class="form-radio" checked>
                            <span>Avion - Normal</span>
                        </label>
                        <label class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900">
                            <input type="radio" name="transport_mode" value="air_express" class="form-radio">
                            <span>Avion - Express</span>
                        </label>
                        <label class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900">
                            <input type="radio" name="transport_mode" value="sea" class="form-radio">
                            <span>Bateau - Groupage</span>
                        </label>
                    </div>
                </div>

                <!-- Nature du colis -->
                <div class="space-y-2 mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nature du colis</label>
                    <select name="content_type" class="input-select" required>
                        <option value="">Sélectionnez...</option>
                        <option value="electronics">Électronique</option>
                        <option value="clothing">Vêtements</option>
                        <option value="miscellaneous">Produits divers</option>
                        <option value="ingredients">Ingredients</option>
                        <option value="divers">Divers</option>
                        <option value="food">Denrées alimentaires</option>
                        <option value="mechanical_parts">Pièces mécaniques</option>
                        <option value="other">Autre</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="space-y-2 mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description du contenu</label>
                    <textarea id="content_description" name="content_description" rows="3" class="input-text" placeholder="Ex: téléphones, chaussures, pièces auto" required></textarea>
                </div>

                <!-- Valeur déclarée -->
                <div class="space-y-2 mb-4">
                    <label class="block text-sm font-medium text-gray-700">Valeur déclarée (FCFA)</label>
                    <input type="number" name="declared_value" min="0" step="0.01" class="input-text">
                </div>

                <!-- Coût des cartons -->
                <div class="space-y-2 mb-4">
                    <label class="block text-sm font-medium text-gray-700">Coût des cartons (FCFA)</label>
                    <input type="number" name="cartons_cost" min="0" step="0.01" value="0" class="input-text">
                    <p class="text-xs text-gray-500">Coût estimé des cartons pour cette expédition</p>
                </div>

                <!-- Option de génération de facture -->
                <div class="space-y-2 mb-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="create_invoice" value="1" class="form-checkbox">
                        <span class="text-sm font-medium text-gray-700">Générer automatiquement la facture</span>
                    </label>
                    <p class="text-xs text-gray-500">Cochez cette case pour créer une facture basée sur les informations de la simulation</p>
                </div>
        </div>
    </div>

    <!-- Étape 2 - Dimensions & poids -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Étape 2 – Dimensions & poids</h2>
            <p class="mt-1 text-sm text-blue-700">Important pour le récapitulatif (le poids final sera confirmé à l'entrepôt).</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Poids estimé (kg)</label>
                    <input type="number" name="estimated_weight" min="0" step="0.1" class="input-text">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Longueur (cm)</label>
                    <input type="number" name="length" min="0" class="input-text">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Largeur (cm)</label>
                    <input type="number" name="width" min="0" class="input-text">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Hauteur (cm)</label>
                    <input type="number" name="height" min="0" class="input-text">
                </div>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                Le prix final sera calculé sur le poids réel ou volumétrique après réception.
            </div>
        </div>
    </div>

    <!-- Étape 3 - Origine & destination -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Étape 3 – Origine & destination</h2>
            <p class="mt-1 text-sm text-blue-700">Indique le départ et l’arrivée (retrait ou livraison).</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-4">
                    <h3 class="text-md font-medium text-gray-700">Adresse d'origine</h3>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Pays</label>
                        <select name="origin_country" class="input-select" required>
                            <option value="">Sélectionnez un pays</option>
                            <option value="AF">Afghanistan</option>
                            <option value="ZA">Afrique du Sud</option>
                            <option value="AL">Albanie</option>
                            <option value="DZ">Algérie</option>
                            <option value="DE">Allemagne</option>
                            <option value="AD">Andorre</option>
                            <option value="AO">Angola</option>
                            <option value="AI">Anguilla</option>
                            <option value="AQ">Antarctique</option>
                            <option value="AG">Antigua-et-Barbuda</option>
                            <option value="SA">Arabie Saoudite</option>
                            <option value="AR">Argentine</option>
                            <option value="AM">Arménie</option>
                            <option value="AW">Aruba</option>
                            <option value="AU">Australie</option>
                            <option value="AT">Autriche</option>
                            <option value="AZ">Azerbaïdjan</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahreïn</option>
                            <option value="BD">Bangladesh</option>
                            <option value="BB">Barbade</option>
                            <option value="BE">Belgique</option>
                            <option value="BZ">Belize</option>
                            <option value="BJ">Bénin</option>
                            <option value="BM">Bermudes</option>
                            <option value="BT">Bhoutan</option>
                            <option value="BY">Biélorussie</option>
                            <option value="BO">Bolivie</option>
                            <option value="BA">Bosnie-Herzégovine</option>
                            <option value="BW">Botswana</option>
                            <option value="BR">Brésil</option>
                            <option value="BN">Brunei</option>
                            <option value="BG">Bulgarie</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="KH">Cambodge</option>
                            <option value="CM">Cameroun</option>
                            <option value="CA">Canada</option>
                            <option value="CV">Cap-Vert</option>
                            <option value="CF">Centrafrique</option>
                            <option value="CL">Chili</option>
                            <option value="CN" selected>Chine</option>
                            <option value="CY">Chypre</option>
                            <option value="CO">Colombie</option>
                            <option value="KM">Comores</option>
                            <option value="CG">Congo-Brazzaville</option>
                            <option value="CD">Congo-Kinshasa</option>
                            <option value="KR">Corée du Sud</option>
                            <option value="KP">Corée du Nord</option>
                            <option value="CR">Costa Rica</option>
                            <option value="CI">Côte d'Ivoire</option>
                            <option value="HR">Croatie</option>
                            <option value="CU">Cuba</option>
                            <option value="DK">Danemark</option>
                            <option value="DJ">Djibouti</option>
                            <option value="DM">Dominique</option>
                            <option value="EG">Égypte</option>
                            <option value="SV">El Salvador</option>
                            <option value="AE">Émirats Arabes Unis</option>
                            <option value="EC">Équateur</option>
                            <option value="ER">Érythrée</option>
                            <option value="ES">Espagne</option>
                            <option value="EE">Estonie</option>
                            <option value="US">États-Unis</option>
                            <option value="ET">Éthiopie</option>
                            <option value="FJ">Fidji</option>
                            <option value="FI">Finlande</option>
                            <option value="FR" selected>France</option>
                            <option value="GA">Gabon</option>
                            <option value="GM">Gambie</option>
                            <option value="GE">Géorgie</option>
                            <option value="GH">Ghana</option>
                            <option value="GR">Grèce</option>
                            <option value="GD">Grenade</option>
                            <option value="GT">Guatemala</option>
                            <option value="GN">Guinée</option>
                            <option value="GQ">Guinée Équatoriale</option>
                            <option value="GW">Guinée-Bissau</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haïti</option>
                            <option value="HN">Honduras</option>
                            <option value="HU">Hongrie</option>
                            <option value="IN">Inde</option>
                            <option value="ID">Indonésie</option>
                            <option value="IR">Iran</option>
                            <option value="IQ">Irak</option>
                            <option value="IE">Irlande</option>
                            <option value="IS">Islande</option>
                            <option value="IL">Israël</option>
                            <option value="IT">Italie</option>
                            <option value="JM">Jamaïque</option>
                            <option value="JP">Japon</option>
                            <option value="JO">Jordanie</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KG">Kirghizistan</option>
                            <option value="KI">Kiribati</option>
                            <option value="KW">Koweït</option>
                            <option value="LA">Laos</option>
                            <option value="LS">Lesotho</option>
                            <option value="LV">Lettonie</option>
                            <option value="LB">Liban</option>
                            <option value="LR">Libéria</option>
                            <option value="LY">Libye</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lituanie</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MK">Macédoine</option>
                            <option value="MG">Madagascar</option>
                            <option value="MY">Malaisie</option>
                            <option value="MW">Malawi</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malte</option>
                            <option value="MA">Maroc</option>
                            <option value="MU">Maurice</option>
                            <option value="MR">Mauritanie</option>
                            <option value="MX">Mexique</option>
                            <option value="MD">Moldavie</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolie</option>
                            <option value="ME">Monténégro</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Namibie</option>
                            <option value="NR">Nauru</option>
                            <option value="NP">Népal</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigéria</option>
                            <option value="NO">Norvège</option>
                            <option value="NZ">Nouvelle-Zélande</option>
                            <option value="OM">Oman</option>
                            <option value="UG">Ouganda</option>
                            <option value="UZ">Ouzbékistan</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palaos</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papouasie-Nouvelle-Guinée</option>
                            <option value="PY">Paraguay</option>
                            <option value="NL">Pays-Bas</option>
                            <option value="PE">Pérou</option>
                            <option value="PH">Philippines</option>
                            <option value="PL">Pologne</option>
                            <option value="PT">Portugal</option>
                            <option value="QA">Qatar</option>
                            <option value="RO">Roumanie</option>
                            <option value="GB">Royaume-Uni</option>
                            <option value="RU">Russie</option>
                            <option value="RW">Rwanda</option>
                            <option value="KN">Saint-Christophe-et-Niévès</option>
                            <option value="SM">Saint-Marin</option>
                            <option value="VC">Saint-Vincent-et-les-Grenadines</option>
                            <option value="LC">Sainte-Lucie</option>
                            <option value="WS">Samoa</option>
                            <option value="SN">Sénégal</option>
                            <option value="RS">Serbie</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapour</option>
                            <option value="SK">Slovaquie</option>
                            <option value="SI">Slovénie</option>
                            <option value="SO">Somalie</option>
                            <option value="SD">Soudan</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SE">Suède</option>
                            <option value="CH">Suisse</option>
                            <option value="SR">Suriname</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SY">Syrie</option>
                            <option value="TJ">Tadjikistan</option>
                            <option value="TZ">Tanzanie</option>
                            <option value="TD">Tchad</option>
                            <option value="TH">Thaïlande</option>
                            <option value="TL">Timor Oriental</option>
                            <option value="TG">Togo</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinité-et-Tobago</option>
                            <option value="TN">Tunisie</option>
                            <option value="TM">Turkménistan</option>
                            <option value="TR">Turquie</option>
                            <option value="UA">Ukraine</option>
                            <option value="UY">Uruguay</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VA">Vatican</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Viêt Nam</option>
                            <option value="YE">Yémen</option>
                            <option value="ZM">Zambie</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Ville</label>
                        <input type="text" name="origin_city" class="input-text" required>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-md font-medium text-gray-700">Destination</h3>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Pays</label>
                        <select name="destination_country" class="input-select" required>
                            <option value="">Sélectionnez un pays</option>
                            <option value="AF">Afghanistan</option>
                            <option value="ZA">Afrique du Sud</option>
                            <option value="AL">Albanie</option>
                            <option value="DZ">Algérie</option>
                            <option value="DE">Allemagne</option>
                            <option value="AD">Andorre</option>
                            <option value="AO">Angola</option>
                            <option value="AI">Anguilla</option>
                            <option value="AQ">Antarctique</option>
                            <option value="AG">Antigua-et-Barbuda</option>
                            <option value="SA">Arabie Saoudite</option>
                            <option value="AR">Argentine</option>
                            <option value="AM">Arménie</option>
                            <option value="AW">Aruba</option>
                            <option value="AU">Australie</option>
                            <option value="AT">Autriche</option>
                            <option value="AZ">Azerbaïdjan</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahreïn</option>
                            <option value="BD">Bangladesh</option>
                            <option value="BB">Barbade</option>
                            <option value="BE">Belgique</option>
                            <option value="BZ">Belize</option>
                            <option value="BJ">Bénin</option>
                            <option value="BM">Bermudes</option>
                            <option value="BT">Bhoutan</option>
                            <option value="BY">Biélorussie</option>
                            <option value="BO">Bolivie</option>
                            <option value="BA">Bosnie-Herzégovine</option>
                            <option value="BW">Botswana</option>
                            <option value="BR">Brésil</option>
                            <option value="BN">Brunei</option>
                            <option value="BG">Bulgarie</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="KH">Cambodge</option>
                            <option value="CM">Cameroun</option>
                            <option value="CA">Canada</option>
                            <option value="CV">Cap-Vert</option>
                            <option value="CF">Centrafrique</option>
                            <option value="CL">Chili</option>
                            <option value="CN">Chine</option>
                            <option value="CY">Chypre</option>
                            <option value="CO">Colombie</option>
                            <option value="KM">Comores</option>
                            <option value="CG">Congo-Brazzaville</option>
                            <option value="CD">Congo-Kinshasa</option>
                            <option value="KR">Corée du Sud</option>
                            <option value="KP">Corée du Nord</option>
                            <option value="CR">Costa Rica</option>
                            <option value="CI">Côte d'Ivoire</option>
                            <option value="HR">Croatie</option>
                            <option value="CU">Cuba</option>
                            <option value="DK">Danemark</option>
                            <option value="DJ">Djibouti</option>
                            <option value="DM">Dominique</option>
                            <option value="EG">Égypte</option>
                            <option value="SV">El Salvador</option>
                            <option value="AE">Émirats Arabes Unis</option>
                            <option value="EC">Équateur</option>
                            <option value="ER">Érythrée</option>
                            <option value="ES">Espagne</option>
                            <option value="EE">Estonie</option>
                            <option value="US">États-Unis</option>
                            <option value="ET">Éthiopie</option>
                            <option value="FJ">Fidji</option>
                            <option value="FI">Finlande</option>
                            <option value="FR" selected>France</option>
                            <option value="GA">Gabon</option>
                            <option value="GM">Gambie</option>
                            <option value="GE">Géorgie</option>
                            <option value="GH">Ghana</option>
                            <option value="GR">Grèce</option>
                            <option value="GD">Grenade</option>
                            <option value="GT">Guatemala</option>
                            <option value="GN">Guinée</option>
                            <option value="GQ">Guinée Équatoriale</option>
                            <option value="GW">Guinée-Bissau</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haïti</option>
                            <option value="HN">Honduras</option>
                            <option value="HU">Hongrie</option>
                            <option value="IN">Inde</option>
                            <option value="ID">Indonésie</option>
                            <option value="IR">Iran</option>
                            <option value="IQ">Irak</option>
                            <option value="IE">Irlande</option>
                            <option value="IS">Islande</option>
                            <option value="IL">Israël</option>
                            <option value="IT">Italie</option>
                            <option value="JM">Jamaïque</option>
                            <option value="JP">Japon</option>
                            <option value="JO">Jordanie</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KG">Kirghizistan</option>
                            <option value="KI">Kiribati</option>
                            <option value="KW">Koweït</option>
                            <option value="LA">Laos</option>
                            <option value="LS">Lesotho</option>
                            <option value="LV">Lettonie</option>
                            <option value="LB">Liban</option>
                            <option value="LR">Libéria</option>
                            <option value="LY">Libye</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lituanie</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MK">Macédoine</option>
                            <option value="MG">Madagascar</option>
                            <option value="MY">Malaisie</option>
                            <option value="MW">Malawi</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malte</option>
                            <option value="MA">Maroc</option>
                            <option value="MU">Maurice</option>
                            <option value="MR">Mauritanie</option>
                            <option value="MX">Mexique</option>
                            <option value="MD">Moldavie</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolie</option>
                            <option value="ME">Monténégro</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Namibie</option>
                            <option value="NR">Nauru</option>
                            <option value="NP">Népal</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigéria</option>
                            <option value="NO">Norvège</option>
                            <option value="NZ">Nouvelle-Zélande</option>
                            <option value="OM">Oman</option>
                            <option value="UG">Ouganda</option>
                            <option value="UZ">Ouzbékistan</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palaos</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papouasie-Nouvelle-Guinée</option>
                            <option value="PY">Paraguay</option>
                            <option value="NL">Pays-Bas</option>
                            <option value="PE">Pérou</option>
                            <option value="PH">Philippines</option>
                            <option value="PL">Pologne</option>
                            <option value="PT">Portugal</option>
                            <option value="QA">Qatar</option>
                            <option value="RO">Roumanie</option>
                            <option value="GB">Royaume-Uni</option>
                            <option value="RU">Russie</option>
                            <option value="RW">Rwanda</option>
                            <option value="KN">Saint-Christophe-et-Niévès</option>
                            <option value="SM">Saint-Marin</option>
                            <option value="VC">Saint-Vincent-et-les-Grenadines</option>
                            <option value="LC">Sainte-Lucie</option>
                            <option value="WS">Samoa</option>
                            <option value="SN">Sénégal</option>
                            <option value="RS">Serbie</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapour</option>
                            <option value="SK">Slovaquie</option>
                            <option value="SI">Slovénie</option>
                            <option value="SO">Somalie</option>
                            <option value="SD">Soudan</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SE">Suède</option>
                            <option value="CH">Suisse</option>
                            <option value="SR">Suriname</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SY">Syrie</option>
                            <option value="TJ">Tadjikistan</option>
                            <option value="TZ">Tanzanie</option>
                            <option value="TD">Tchad</option>
                            <option value="TH">Thaïlande</option>
                            <option value="TL">Timor Oriental</option>
                            <option value="TG">Togo</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinité-et-Tobago</option>
                            <option value="TN">Tunisie</option>
                            <option value="TM">Turkménistan</option>
                            <option value="TR">Turquie</option>
                            <option value="UA">Ukraine</option>
                            <option value="UY">Uruguay</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VA">Vatican</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Viêt Nam</option>
                            <option value="YE">Yémen</option>
                            <option value="ZM">Zambie</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Ville</label>
                        <input type="text" name="destination_city" class="input-text" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Mode de retrait</label>
                        <select name="delivery_option" class="input-select">
                            <option value="pickup">Retrait en agence</option>
                            <option value="delivery">Livraison</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Étape 4 - Documents & images -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Étape 4 – Documents & images (optionnel)</h2>
            <p class="mt-1 text-sm text-blue-700">Si tu n’as pas de photos, elles seront prises à l’entrepôt.</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Facture / Invoice</label>
                    <input type="file" name="invoice_file" class="input-file">
                    <p class="text-xs text-gray-500">Formats acceptés: PDF, JPG, PNG. Max 5MB.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Images du colis</label>
                    <input type="file" name="photos[]" multiple accept="image/*" class="input-file">
                    <p class="text-xs text-gray-500">Les images seront prises automatiquement à l'entrepôt si non fournies.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Étape 5 - Résumé & estimation -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Étape 5 – Résumé & estimation</h2>
            <p class="mt-1 text-sm text-blue-700">Estimation indicative. Le montant final sera confirmé après réception.</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Mode de transport</p>
                        <p id="step5-summary-transport" class="font-medium">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Délai estimé</p>
                        <p id="step5-summary-delivery" class="font-medium">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estimation du poids</p>
                        <p id="step5-summary-weight" class="font-medium">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estimation du coût</p>
                        <p id="step5-summary-cost" class="font-medium">-</p>
                    </div>
                </div>

                <p class="text-sm text-gray-500">💡 Le montant définitif sera communiqué après réception et pesée réelle.</p>
            </div>
        </div>
    </div>

    <!-- Bloc informatif -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <div class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Certains produits sont interdits ou soumis à conditions. Les frais de douane ne sont pas inclus. Les colis refusés seront signalés immédiatement. Le paiement s'effectue après confirmation du poids.
                    <a href="#" class="font-medium underline text-blue-600 hover:text-blue-500">Liste des produits interdits</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-3">
            <button type="submit" id="submitBtn" class="btn-primary rounded-xl px-5 py-3">
                Soumettre l'envoi
            </button>
            <button type="button" id="generateInvoiceBtn" class="btn-outline-primary rounded-xl px-5 py-3 hidden" onclick="generateInvoiceOnly()">
                Générer la facture uniquement
            </button>
        </div>
        <div id="invoiceStatus" class="text-sm text-gray-600 hidden">
            <span class="inline-flex items-center gap-1">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Préparation de la facture...
            </span>
        </div>
    </div>
    </form>

        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-6 space-y-4">
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="text-sm font-semibold text-gray-900">Récapitulatif</div>
                        <div class="text-xs text-blue-700">Estimation indicative</div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-xs font-semibold text-gray-500 uppercase">Mode</div>
                            <div id="sidebar-summary-transport" class="mt-1 text-sm font-semibold text-gray-900">-</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">Poids</div>
                                <div id="sidebar-summary-weight" class="mt-1 text-sm font-semibold text-gray-900">-</div>
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">Délai</div>
                                <div id="sidebar-summary-delivery" class="mt-1 text-sm font-semibold text-gray-900">-</div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <div class="text-xs font-semibold text-blue-700 uppercase">Coût estimé</div>
                            <div id="sidebar-summary-cost" class="mt-1 text-lg font-semibold text-blue-900">-</div>
                        </div>
                        <div class="text-xs text-gray-500">
                            Le montant définitif sera communiqué après réception et pesée réelle.
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

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
            const form = document.getElementById('shipmentForm');
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
<script>
    // Script pour mettre à jour le résumé en temps réel
    document.addEventListener('DOMContentLoaded', function() {
        // Écouteurs pour mettre à jour le résumé
        document.querySelectorAll('input[name="transport_mode"]').forEach(radio => {
            radio.addEventListener('change', updateSummary);
        });

        document.querySelector('input[name="estimated_weight"]').addEventListener('input', updateSummary);

        function updateSummary() {
            const transportMode = document.querySelector('input[name="transport_mode"]:checked').value;
            const weight = document.querySelector('input[name="estimated_weight"]').value;

            // Mise à jour du transport
            let transportText = '';
            let deliveryText = '';
            let costFcfa = null;

            switch(transportMode) {
                case 'air_normal':
                    transportText = 'Avion - Normal';
                    deliveryText = '5-7 jours';
                    costFcfa = weight ? (parseFloat(weight) * 5250) : null; // 5250 FCFA/kg
                    break;
                case 'air_express':
                    transportText = 'Avion - Express';
                    deliveryText = '2-3 jours';
                    costFcfa = weight ? (parseFloat(weight) * 7875) : null; // 7875 FCFA/kg
                    break;
                case 'sea':
                    transportText = 'Bateau - Groupage';
                    deliveryText = '15-30 jours';
                    costFcfa = weight ? (parseFloat(weight) * 2625) : null; // 2625 FCFA/kg
                    break;
            }

            const weightText = weight ? weight + ' kg' : '-';

            function formatMoney(amountFcfa) {
                if (amountFcfa === null || Number.isNaN(amountFcfa)) return '-';
                return Math.round(amountFcfa).toLocaleString('fr-FR') + ' FCFA';
            }

            const costText = formatMoney(costFcfa);

            const ids = {
                transport: ['step5-summary-transport', 'sidebar-summary-transport'],
                delivery: ['step5-summary-delivery', 'sidebar-summary-delivery'],
                weight: ['step5-summary-weight', 'sidebar-summary-weight'],
                cost: ['step5-summary-cost', 'sidebar-summary-cost'],
            };

            ids.transport.forEach((id) => { const el = document.getElementById(id); if (el) el.textContent = transportText; });
            ids.delivery.forEach((id) => { const el = document.getElementById(id); if (el) el.textContent = deliveryText; });
            ids.weight.forEach((id) => { const el = document.getElementById(id); if (el) el.textContent = weightText; });
            ids.cost.forEach((id) => { const el = document.getElementById(id); if (el) el.textContent = costText; });
        }

        updateSummary();

        window.addEventListener('currencyChange', () => updateSummary());
    });

    // Gestion de la case à cocher pour la génération de facture
    document.addEventListener('DOMContentLoaded', function() {
        const createInvoiceCheckbox = document.querySelector('input[name="create_invoice"]');
        const submitBtn = document.getElementById('submitBtn');
        const generateInvoiceBtn = document.getElementById('generateInvoiceBtn');
        const invoiceStatus = document.getElementById('invoiceStatus');
        const shipmentForm = document.getElementById('shipmentForm');

        createInvoiceCheckbox.addEventListener('change', function() {
            if (this.checked) {
                submitBtn.textContent = 'Soumettre et générer la facture';
                submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                submitBtn.classList.remove('btn-primary');

                // Afficher un indicateur dans le résumé
                const sidebar = document.querySelector('.sticky.top-6');
                if (sidebar) {
                    const invoiceIndicator = document.createElement('div');
                    invoiceIndicator.className = 'bg-green-50 border border-green-200 rounded-xl p-3 mb-4';
                    invoiceIndicator.innerHTML = `
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-800">Facture sera générée automatiquement</span>
                        </div>
                    `;
                    sidebar.insertBefore(invoiceIndicator, sidebar.firstChild);
                }
            } else {
                submitBtn.textContent = 'Soumettre l\'envoi';
                submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                submitBtn.classList.add('btn-primary');

                // Supprimer l'indicateur
                const indicator = document.querySelector('.bg-green-50.border-green-200');
                if (indicator) {
                    indicator.remove();
                }
            }
        });

        // Fonction pour générer la facture uniquement (optionnel)
        window.generateInvoiceOnly = function() {
            if (confirm('Voulez-vous générer une facture basée sur les informations actuelles ?')) {
                // Cocher la case et soumettre
                createInvoiceCheckbox.checked = true;
                createInvoiceCheckbox.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    shipmentForm.submit();
                }, 500);
            }
        };

        // Mise à jour du texte du bouton en fonction du poids
        const weightInput = document.querySelector('input[name="estimated_weight"]');
        if (weightInput) {
            weightInput.addEventListener('input', function() {
                if (createInvoiceCheckbox.checked && this.value > 0) {
                    const cost = calculateEstimatedCost(this.value);
                    submitBtn.innerHTML = `Soumettre et générer la facture (${cost} FCFA estimé)`;
                }
            });
        }

        // Fonction pour calculer le coût estimé
        function calculateEstimatedCost(weight) {
            const transportMode = document.querySelector('input[name="transport_mode"]:checked').value;
            const declaredValue = parseFloat(document.querySelector('input[name="declared_value"]').value) || 0; // Déjà en FCFA
            const cartonsCost = parseFloat(document.querySelector('input[name="cartons_cost"]').value) || 0; // Déjà en FCFA

            let shippingCost = 0;
            switch (transportMode) {
                case 'air_normal':
                    shippingCost = weight * 5250; // 5250 FCFA/kg
                    break;
                case 'air_express':
                    shippingCost = weight * 7875; // 7875 FCFA/kg
                    break;
                case 'sea':
                    shippingCost = weight * 2625; // 2625 FCFA/kg
                    break;
            }

            const customsFee = declaredValue > 0 ? declaredValue * 0.15 : 0; // 15% sur la valeur en FCFA
            const handlingFee = 2500; // Frais fixes en FCFA
            const insuranceFee = declaredValue > 0 ? declaredValue * 0.02 : 0; // 2% sur la valeur en FCFA
            const otherFees = 1000; // Frais fixes en FCFA

            const total = Math.round(shippingCost + customsFee + handlingFee + insuranceFee + otherFees + cartonsCost);
            return total.toLocaleString('fr-FR');
        }
    });
</script>
@endsection
