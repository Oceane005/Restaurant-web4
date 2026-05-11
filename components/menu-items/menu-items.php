<?php
$entrees = [
  ['nom' => 'Edamame au sel de mer fumé',      'desc' => 'Fèves de soja vapeur, sel fumé et zests de yuzu',            'prix' => '10', 'img' => '/img/Edamame_au_sel_de_mer_fume.png'],
  ['nom' => 'Tataki de thon rouge',             'desc' => 'Thon saisi, sauce ponzu, gingembre mariné',                  'prix' => '16', 'img' => '/img/tataki_thon_roupe.png'],
  ['nom' => 'Gyoza de porc et crevettes',       'desc' => 'Gyozas grillés, sauce miso épicée',                          'prix' => '14', 'img' => '/img/Gyoza_de_porc_et_crevettes.png'],
  ['nom' => 'Salade wakame et sésame noir',     'desc' => 'Algues marines, vinaigrette soja-sésame',                    'prix' => '15', 'img' => '/img/Salade_wakame_et_sesame_noir.png'],
  ['nom' => 'Soupe miso traditionnelle',        'desc' => 'Bouillon miso, tofu soyeux, wakame, oignons verts',          'prix' => '8',  'img' => '/img/Soupe_miso_traditionel.png'],
  ['nom' => 'Tempura de crevettes',             'desc' => 'Pâte croustillante, sauce tentsuyu',                         'prix' => '14', 'img' => '/img/tempura_de_crevette.png'],
  ['nom' => 'Tartare de saumon façon japonaise','desc' => 'Saumon frais, huile de sésame, sauce tobiko',                'prix' => '18', 'img' => '/img/tartar_de_saumon_facon_japonnaise.png'],
  ['nom' => 'Yakitori de poulet',               'desc' => 'Brochettes de poulet laqué, sauce tare maison',              'prix' => '16', 'img' => '/img/Yakitori_de_poulet.png'],
];
?>
<section class="menu-section">
  <div class="menu-watermark" aria-hidden="true">
    <?php for($i=0;$i<12;$i++): ?><img src="/img/daruma.png" alt=""><?php endfor; ?>
  </div>
  <ul class="menu-list">
    <?php foreach($entrees as $i => $item): ?>
    <li class="menu-item<?= $i%2===1 ? ' menu-item--reverse' : '' ?>">
      <div class="menu-item-content">
        <h2 class="menu-item-nom"><?= htmlspecialchars($item['nom']) ?></h2>
        <p class="menu-item-desc"><?= htmlspecialchars($item['desc']) ?></p>
        <span class="menu-item-prix"><?= $item['prix'] ?>$</span>
      </div>
      <div class="menu-item-img-wrap">
        <img src="<?= $item['img'] ?>" alt="<?= htmlspecialchars($item['nom']) ?>" class="menu-item-img" loading="lazy">
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</section>