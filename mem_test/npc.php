<?php
 
$zone = [
//ID1		ID2 	EN 	JP FR DE
 
"340" => [
[0, 		901, 	"Striking Dummy", "木人", "Mannequin d'entraînement", "Holzpuppe", true], //541
],


//Turn 10
"193" => [
[4894346, 3285, "Imdugud", "イムドゥグド", "Imdugud", "Imdugud", true], //3192
[4903049, 3286, "Daughter of Imdugud", "イムドゥグド・ドーター", "Fille d'Imdugud", "Imduguds Tochter", false], //3193
[4903050, 3287, "Son of Imdugud", "イムドゥグド・サン", "Fils d'Imdugud", "Imduguds Sohn", false],//3194
[4982906, 3314, "Mechanic1", "Mechanic1", "Mechanic1", "Mechanic1", false],
[4982907, 3314, "Mechanic2", "Mechanic2", "Mechanic2", "Mechanic2", false]
],  

//Turn 11

"194" => [
[4895912, 3289, "Kaliya", "カーリア", "Kaliya", "Kaliya", true], //3197
[4904185, 3290, "Security Node", "護衛システム", "Sphère d'escorte", "Sicherheits-Sphäre", false], //3198
[4904186, 3291, "Weapons Node", "砲撃システム", "Sphère d'armement", "Waffen-Sphäre", false], //3199
[4904188, 3292, "Gravity Node", "重力システム", "Module de gravité", "Schwerkraft-Modul", false], //3200
[4904187, 3293, "Electric Node", "雷撃システム", "Sphère d'électrochoc", "Elektrisch Modul", false], //3201
],


//Turn 12
"195" => [
[4895915, 3295, "Phoenix", "フェニックス", "Phénix", "Phönix", true], //3204
[4904777 ,3296, "Blackfire", "漆黒の炎", "Flamme noire", "Schwarzfeuer", false],
[4904780, 3297, "Redfire", "紅蓮の炎", "Flamme rouge", "Rotfeuer", false], //3205
[4904784, 3298, "Fountain of Fire", "霊泉の炎", "Flamme de la vie", "Feuerquelle", false], //3206
[4904785, 3299, "Phoenix-Egi", "フェニックス・エギ", "Phénix-Egi", "Phönix-Egi", false], //3208
[4904782, 3301, "Bennu", "ベンヌ", "Bénou", "Bennu", false], //3207 RES
[4904764, 3300, "Bennu", "ベンヌ", "Bénou", "Bennu", false], //SMALL
[4904783 ,3302, "Bennu", "ベンヌ", "Bénou", "Bennu", false], //3209 //BIG
],

//Turn 13
"196" => [
[4895917, 3304, "Bahamut Prime", "バハムート・プライム", "Primo-Bahamut", "Prim-Bahamut", true],
[4904801, 3305, "Dark Aether", "ダークエーテル", "éther sombre", "Dunkeläther", false], //3211
[4904786, 3306, "The Shadow of Meracydia", "メラシディアン・シャドウ", "Ombre de Meracydia", "Schatten von Meracydia", false], //3212
[4904836, 3307, "The Storm of Meracydia", "メラシディアン・ストーム", "Tempête de Meracydia", "Sturm von Meracydia", false],
[5119545, 3526, "The Storm of Meracydia", "メラシディアン・ストーム", "Tempête de Meracydia", "Sturm von Meracydia", false], //3213
[4904848, 3310, "The Blood of Meracydia", "メラシディアン・ブラッド", "Sang de Meracydia", "Blut von Meracydia", false], //3215
[4904849, 3311, "The Sin of Meracydia", "メラシディアン・シン", "Péché de Meracydia", "Sünde von Meracydia", false], //3216
[4904850, 3312, "The Gust of Meracydia", "メラシディアン・ガスト", "Bourrasque de Meracydia", "Wind von Meracydia", false], //3217
[4904847, 3309, "The Pain of Meracydia", "メラシディアン・ペイン", "Douleur de Meracydia", "Schmerz von Meracydia", false], //3214
 
]   
 
];

$__NPC = [
[0, 1008,	"Eos", "フェアリー・エオス", "Eos", "Eos"], //1398
[0,	1009,	"Selene", "フェアリー・セレネ", "Selene", "Selene"], //1399
[0, 1011, 	"Topaz Carbuncle", "カーバンクル・トパーズ", "Carbuncle topaze", "Topas-Karfunkel"], //1400
[0, 1012,	"Emerald Carbuncle", "カーバンクル・エメラルド", "Carbuncle émeraude", "Smaragd-Karfunkel"], //1401
[0, 1013,	"Ifrit-Egi", "イフリート・エギ", "Ifrit-Egi", "Ifrit-Egi"], //1402
[0, 1014,	"Titan-Egi", "タイタン・エギ", "Titan-Egi", "Titan-Egi"], //1403
[0, 1015, 	"Garuda-Egi", "ガルーダ・エギ", "Garuda-Egi", "Garuda-Egi"], //1404

];



foreach($__NPC as $e) {
	apc_store('en:n:'.$e[0].'_'.$e[1], $e[2]);
	apc_store('ja:n:'.$e[0].'_'.$e[1], $e[3]);
	apc_store('fr:n:'.$e[0].'_'.$e[1], $e[4]);
	apc_store('de:n:'.$e[0].'_'.$e[1], $e[5]);
}


foreach ($zone as $e => $npc) {
	$pack = [];
	foreach($npc as $npclang) {
		 $pack[$npclang[0]."_".$npclang[1]] = [$npclang[2], $npclang[6]];
	}
	apc_store('en:z:'.$e, $pack);


	$pack = [];
	foreach($npc as $npclang) {
		 $pack[$npclang[0]."_".$npclang[1]] = [$npclang[3], $npclang[6]];
	}
	apc_store('ja:z:'.$e, $pack);


	$pack = [];
	foreach($npc as $npclang) {
		 $pack[$npclang[0]."_".$npclang[1]] = [$npclang[4], $npclang[6]];
	}
	apc_store('fr:z:'.$e, $pack);

	$pack = [];
	foreach($npc as $npclang) {
		 $pack[$npclang[0]."_".$npclang[1]] = [$npclang[5], $npclang[6]];
	}
	apc_store('de:z:'.$e, $pack);
}

 

?>