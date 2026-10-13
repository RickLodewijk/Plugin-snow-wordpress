# ❄️ Rick Winter Snow & Theme

Een modulaire WordPress plugin die een sfeervol **Snow Falling JS** effect (gebaseerd op [Boyd Massie's CodePen](https://codepen.io/massiebn/pen/OJMbXz)) en een bijpassende **donkere winterse nacht layout** toevoegt aan elke pagina van je WordPress website.

## ✨ Functionaliteiten

* **Authentiek Sneeuweffect:** Geïmplementeerd met GPU-versnelling (`useGPU: true`), reageert vloeiend op de muis en gebruikt `pointer-events: none` zodat knoppen en links 100% klikbaar blijven.
* **Donker Winter Thema:** Activeert automatisch een diepe nachtblauwe achtergrond (`#080d1a`), frosted glass header en dark-mode kaarten zodra het effect aan staat.
* **Per Pagina Instelbaar:** Schakel het effect per individuele pagina, post, recept of custom post type in via de zijbalk in de WordPress editor.
* **ACF & Native Meta-Box:** Ondersteunt zowel Advanced Custom Fields (ACF) als een ingebouwde native WordPress meta-box fallback.
* **Globale Activatie:** Eenvoudig standaard aanzetten voor de hele website via **Instellingen ➔ ❄️ Winter Sneeuw**.

## 📁 Structuur

```text
rick-winter-snow/
├── assets/
│   ├── css/
│   │   └── winter-theme.css      # Donkere winterse layout stijlen
│   └── js/
│       └── snowstorm.min.js      # Snow Falling JS bibliotheek
├── includes/
│   ├── admin.php                 # ACF velden, meta-box en instellingenmenu
│   └── frontend.php              # Conditioneel inladen en body-classes
├── composer.json
├── README.md
└── rick-winter-snow.php          # Hoofd pluginbestand
```

## 🚀 Installatie

1. Plaats de map `rick-winter-snow` in `wp-content/plugins/` (of `web/app/plugins/` in Bedrock).
2. Activeer de plugin via het WordPress dashboard onder **Plugins**.
3. Bewerk een pagina en zet **Sneeuw & Winter Thema Inschakelen** op **Aan**.

## 📄 Licentie

GPL-2.0-or-later.
