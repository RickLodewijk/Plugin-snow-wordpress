# ❄️🍂 Rick Seizoens Thema's & Effecten (Winter & Herfst)

Een krachtige en modulaire WordPress plugin die een sfeervol **Snow Falling JS** effect, een GPU-versnelde **Autumn Leaves JS** vallende bladeren animatie en een selectie aan sfeervolle **winter- en herfstthema's** toevoegt aan je WordPress website.

## ✨ Functionaliteiten

* **Dashboard met Tabbladen:**
  * ❄️ **Winter Sneeuw & Thema's:** Keuze uit winterthema's en sneeuwval-instellingen.
  * 🍂 **Herfst Bladeren & Thema's:** Keuze uit herfstthema's en vallende bladeren opties.
  * 📄 **Bereik & Pagina Toewijzing:** Centraal beheer om per pagina of berichttype een specifiek seizoensthema toe te wijzen.
* **Twee Interactieve Seizoenseffecten:**
  * ❄️ **Snow Falling JS:** Witte sneeuwvlokken met muisinteractie en GPU-versnelling.
  * 🍂 **Autumn Leaves JS:** Realistische esdoorn-, eiken- en beukenbladeren in warme herfsttinten (rood, oranje, goud en kastanje) die natuurlijk dwarrelen en reageren op de muis.
* **4 Winterthema's:**
  * 🌌 **Donkere Winternacht (`winter-dark`):** Diepe nachtblauwe achtergrond (`#080d1a`), frosted glass header en dark-mode kaarten.
  * ❄️ **Frosted Winterdag (`winter-frost`):** Frisse ijsblauwe dagsetting (`#edf5fa`), heldere frosted kaarten en donkere typografie.
  * 🌌 **Noorderlicht / Aurora (`winter-aurora`):** Magische polarnacht met smaragdgroene en paarse aurora-gloed.
  * ❄️ **Alleen Sneeuweffect (`snow-only`):** Behoud de originele site en laat alleen sneeuw vallen.
* **4 Herfstthema's:**
  * 🍁 **Gouden Herfstzon / Warm Amber (`autumn-gold`):** Warme gouden en amberkleurige herfstsfeer (`#1a1008`), warme gloed en dwarrelende bladeren.
  * 🍂 **Donker Herfstbos & Koper (`autumn-dark`):** Diep kastanjebruin en koper nachtbos (`#130a05`) met warme herfstaccenten.
  * 🎃 **Pompoen & Kaneel (`autumn-pumpkin`):** Sfeervolle pompoenoranje en kaneelbruine setting (`#170b04`).
  * 🍂 **Alleen Vallende Bladeren (`leaves-only`):** Behoud de originele styling en laat uitsluitend herfstbladeren vallen.
* **Granulair Bereik & Toewijzing:**
  * Kies voor de hele website of alleen specifieke berichttypen of pagina's.
  * Ook rechtstreeks in te stellen in de Gutenberg / ACF pagina-editor zijbalk per individueel recept, pagina, pepernoot of oliebol.

## 📁 Structuur

```text
rick-winter-snow/
├── assets/
│   ├── css/
│   │   ├── autumn-theme.css      # Herfstthema stijlen (Gold, Dark Forest, Pumpkin)
│   │   └── winter-theme.css      # Winterthema stijlen (Dark, Frost, Aurora)
│   └── js/
│       ├── autumn-leaves.min.js  # Vallende bladeren animatie (Vanilla JS)
│       └── snowstorm.min.js      # Snow Falling JS bibliotheek
├── includes/
│   ├── admin.php                 # Tabbladen, ACF velden, optiepagina en sanitization
│   └── frontend.php              # Dynamische seizoensdetectie, script-enqueuing en body-classes
├── composer.json
├── README.md
└── rick-winter-snow.php          # Hoofd pluginbestand (v1.2.0)
```

## 🚀 Gebruik

1. Ga in het WordPress dashboard naar **❄️🍂 Seizoenen**.
2. Klik op het tabblad **❄️ Winter Sneeuw** of **🍂 Herfst Bladeren** om het gewenste seizoensthema en effect te configureren.
3. Ga naar het tabblad **📄 Bereik & Pagina Toewijzing** om te bepalen welk thema op welke pagina of berichttype wordt getoond.
4. Klik op **Instellingen Opslaan**.

## 📄 Licentie

GPL-2.0-or-later.
