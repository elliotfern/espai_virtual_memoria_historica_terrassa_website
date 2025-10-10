// i18n/home.ts
import { makeDict } from './i18n';

export type HomeKeys = 'basicSearch_titleA' | 'basicSearch_titleB' | 'basicSearch_placeholder' | 'basicSearch_help' | 'basicSearch_cta' | 'explore_titleA' | 'explore_titleB' | 'explore_text' | 'db_btn_general' | 'db_btn_cost' | 'db_btn_exili' | 'db_btn_repres' | 'hero_title' | 'hero_subtitle' | 'hero_text' | 'hero_cta' | 'resources_title' | 'resources_subtitle' | 'grid_title_general' | 'grid_title_cost' | 'grid_title_exili' | 'grid_title_repres' | 'interactive_title' | 'interactive_item1' | 'interactive_item2' | 'interactive_item3' | 'interactive_item4' | 'team_title' | 'team_subtitle' | 'team_text' | 'team_members';

export const LABELS_HOME = makeDict<HomeKeys>({
  ca: {
    basicSearch_titleA: 'Cerca bàsica de',
    basicSearch_titleB: 'represaliats i represaliades',
    basicSearch_placeholder: 'Nom i cognoms...',
    basicSearch_help: "Si vols fer una cerca avançada, aplicant filtres per lloc de naixement, sexe, afiliació sindical i/o política, entre d'altres, clica al següent botó.",
    basicSearch_cta: 'Cerca avançada',

    explore_titleA: 'Explora les nostres',
    explore_titleB: 'bases de dades',
    explore_text: "Consulta les bases de dades de l'Espai Virtual de la Memòria Històrica de Terrassa, on podràs trobar el llistat complet de represaliats i fer cerques avançades.",
    db_btn_general: 'General',
    db_btn_cost: 'Cost Humà de la Guerra civil',
    db_btn_exili: 'Exiliats i deportats',
    db_btn_repres: 'Represaliats de la dictadura',

    hero_title: 'Espai virtual de la Memòria Històrica de Terrassa',
    hero_subtitle: 'Descobreix les històries, dades<br> i testimonis que conformen<br> la memòria històrica.',
    hero_text: "L'Espai Virtual de la Memòria Històrica de Terrassa (EVMHT) és un espai històric, documental, educatiu i d'investigació que recull el cost humà de la lluita per la llibertat dels terrassencs i terrassenques entre 1936 i 1983.",
    hero_cta: "Coneix més sobre l'Espai Virtual",

    resources_title: 'Explora els nostres recursos',
    resources_subtitle: 'Consulta i interactua amb bases de dades que inclouen fitxes<br> individuals, documents, i estudis històrics',

    grid_title_general: 'Base de dades<br>General',
    grid_title_cost: 'Cost humà <br>de la Guerra civil',
    grid_title_exili: 'Exiliats i<br> deportats',
    grid_title_repres: 'Represaliats <br>de la dictadura',

    interactive_title: 'Recursos interactius',
    interactive_item1: 'Històries personals de les represaliades <br>i represaliats',
    interactive_item2: 'Eines de cerca avançada<br> per explorar arxius',
    interactive_item3: 'Bases de dades detallades<br> segons el tipus de repressió',
    interactive_item4: 'Fonts documentals<br> verificades i accessibles',

    team_title: "Equip d'investigadors i tècnics del web",
    team_subtitle: 'Espai Virtual de la Memòria Històrica de Terrassa',
    team_text: "<span class='negreta'>Qui hi ha darrere del projecte?</span> Aquest projecte és possible gràcies a la col·laboració d'entitats i professionals dedicats a la recuperació de la memòria històrica",
    team_members: 'Membres recerca històrica:',
  },
  es: {
    basicSearch_titleA: 'Búsqueda básica de',
    basicSearch_titleB: 'represaliados y represaliadas',
    basicSearch_placeholder: 'Nombre y apellidos...',
    basicSearch_help: 'Si quieres hacer una búsqueda avanzada, aplicando filtros por lugar de nacimiento, sexo, afiliación sindical y/o política, entre otros, haz clic en el siguiente botón.',
    basicSearch_cta: 'Búsqueda avanzada',

    explore_titleA: 'Explora nuestras',
    explore_titleB: 'bases de datos',
    explore_text: 'Consulta las bases de datos del Espacio Virtual de la Memoria Histórica de Terrassa, donde podrás encontrar el listado completo de represaliados y realizar búsquedas avanzadas.',
    db_btn_general: 'General',
    db_btn_cost: 'Coste humano de la Guerra Civil',
    db_btn_exili: 'Exiliados y deportados',
    db_btn_repres: 'Represaliados de la dictadura',

    hero_title: 'Espacio virtual de la Memoria Histórica de Terrassa',
    hero_subtitle: 'Descubre las historias, datos<br> y testimonios que conforman<br> la memoria histórica.',
    hero_text: 'El Espacio Virtual de la Memoria Histórica de Terrassa (EVMHT) es un espacio histórico, documental, educativo y de investigación que recoge el coste humano de la lucha por la libertad de los terrassenses entre 1936 y 1983.',
    hero_cta: 'Conoce más sobre el Espacio Virtual',

    resources_title: 'Explora nuestros recursos',
    resources_subtitle: 'Consulta e interactúa con bases de datos que incluyen fichas<br> individuales, documentos y estudios históricos',

    grid_title_general: 'Base de datos<br>General',
    grid_title_cost: 'Coste humano <br>de la Guerra Civil',
    grid_title_exili: 'Exiliados y<br> deportados',
    grid_title_repres: 'Represaliados <br>de la dictadura',

    interactive_title: 'Recursos interactivos',
    interactive_item1: 'Historias personales de las personas<br> represaliadas',
    interactive_item2: 'Herramientas de búsqueda avanzada<br> para explorar archivos',
    interactive_item3: 'Bases de datos detalladas<br> por tipo de represión',
    interactive_item4: 'Fuentes documentales<br> verificadas y accesibles',

    team_title: 'Equipo de investigadores y técnicos del web',
    team_subtitle: 'Espacio Virtual de la Memoria Histórica de Terrassa',
    team_text: "<span class='negreta'>¿Quién está detrás del proyecto?</span> Este proyecto es posible gracias a la colaboración de entidades y profesionales dedicados a la recuperación de la memoria histórica",
    team_members: 'Miembros de la investigación histórica:',
  },
  en: {
    basicSearch_titleA: 'Basic search of',
    basicSearch_titleB: 'repressed people',
    basicSearch_placeholder: 'Name and surname...',
    basicSearch_help: 'If you want to run an advanced search, applying filters by place of birth, sex, union and/or political affiliation, among others, click the button below.',
    basicSearch_cta: 'Advanced search',

    explore_titleA: 'Explore our',
    explore_titleB: 'databases',
    explore_text: 'Browse the databases of the Virtual Space of Historical Memory of Terrassa, where you can find the full list of victims and perform advanced searches.',
    db_btn_general: 'General',
    db_btn_cost: 'Human cost of the Civil War',
    db_btn_exili: 'Exiles and deportees',
    db_btn_repres: 'Victims of the dictatorship',

    hero_title: 'Virtual space of the Historical Memory of Terrassa',
    hero_subtitle: 'Discover the stories, data<br> and testimonies that make up<br> the historical memory.',
    hero_text: 'The Virtual Space of the Historical Memory of Terrassa (EVMHT) is a historical, documentary, educational and research space that records the human cost of the struggle for freedom of the people of Terrassa between 1936 and 1983.',
    hero_cta: 'Learn more about the Virtual Space',

    resources_title: 'Explore our resources',
    resources_subtitle: 'Consult and interact with databases including individual<br> records, documents and historical studies',

    grid_title_general: 'Database<br>General',
    grid_title_cost: 'Human cost <br>of the Civil War',
    grid_title_exili: 'Exiles and<br> deportees',
    grid_title_repres: 'Victims <br>of the dictatorship',

    interactive_title: 'Interactive resources',
    interactive_item1: 'Personal stories of the<br> repressed',
    interactive_item2: 'Advanced search tools<br> to explore archives',
    interactive_item3: 'Detailed databases<br> by type of repression',
    interactive_item4: 'Verified and accessible<br> documentary sources',

    team_title: 'Web research and technical team',
    team_subtitle: 'Virtual Space of the Historical Memory of Terrassa',
    team_text: "<span class='negreta'>Who is behind the project?</span> This project is possible thanks to the collaboration of organizations and professionals dedicated to recovering historical memory",
    team_members: 'Historical research members:',
  },
  fr: {
    basicSearch_titleA: 'Recherche basique de',
    basicSearch_titleB: 'personnes réprimées',
    basicSearch_placeholder: 'Nom et prénom...',
    basicSearch_help: 'Pour effectuer une recherche avancée, avec des filtres par lieu de naissance, sexe, affiliation syndicale et/ou politique, entre autres, cliquez sur le bouton ci-dessous.',
    basicSearch_cta: 'Recherche avancée',

    explore_titleA: 'Explorez nos',
    explore_titleB: 'bases de données',
    explore_text: "Consultez les bases de données de l'Espace Virtuel de la Mémoire Historique de Terrassa, où vous trouverez la liste complète des personnes réprimées et pourrez effectuer des recherches avancées.",
    db_btn_general: 'Générale',
    db_btn_cost: 'Coût humain de la Guerre civile',
    db_btn_exili: 'Exilés et déportés',
    db_btn_repres: 'Réprimés de la dictature',

    hero_title: 'Espace virtuel de la Mémoire Historique de Terrassa',
    hero_subtitle: 'Découvrez les histoires, données<br> et témoignages qui composent<br> la mémoire historique.',
    hero_text: "L'Espace Virtuel de la Mémoire Historique de Terrassa (EVMHT) est un espace historique, documentaire, éducatif et de recherche qui recense le coût humain de la lutte pour la liberté des habitants de Terrassa entre 1936 et 1983.",
    hero_cta: "En savoir plus sur l'Espace Virtuel",

    resources_title: 'Explorez nos ressources',
    resources_subtitle: 'Consultez et interagissez avec des bases de données comprenant des fiches<br> individuelles, des documents et des études historiques',

    grid_title_general: 'Base de données<br>Générale',
    grid_title_cost: 'Coût humain <br>de la Guerre civile',
    grid_title_exili: 'Exilés et<br> déportés',
    grid_title_repres: 'Réprimés <br>de la dictature',

    interactive_title: 'Ressources interactives',
    interactive_item1: 'Récits personnels des<br> personnes réprimées',
    interactive_item2: 'Outils de recherche avancée<br> pour explorer les archives',
    interactive_item3: 'Bases de données détaillées<br> par type de répression',
    interactive_item4: 'Sources documentaires<br> vérifiées et accessibles',

    team_title: "Équipe d'enquête et technique du site",
    team_subtitle: 'Espace Virtuel de la Mémoire Historique de Terrassa',
    team_text: "<span class='negreta'>Qui est derrière le projet ?</span> Ce projet est possible grâce à la collaboration d'entités et de professionnels dédiés à la récupération de la mémoire historique",
    team_members: 'Membres de la recherche historique :',
  },
  it: {
    basicSearch_titleA: 'Ricerca di base di',
    basicSearch_titleB: 'persone represse',
    basicSearch_placeholder: 'Nome e cognome...',
    basicSearch_help: 'Se desideri effettuare una ricerca avanzata, applicando filtri per luogo di nascita, sesso, affiliazione sindacale e/o politica, tra gli altri, clicca sul pulsante qui sotto.',
    basicSearch_cta: 'Ricerca avanzata',

    explore_titleA: 'Esplora le nostre',
    explore_titleB: 'banche dati',
    explore_text: "Consulta le banche dati dello Spazio Virtuale della Memoria Storica di Terrassa, dove puoi trovare l'elenco completo dei repressi ed effettuare ricerche avanzate.",
    db_btn_general: 'Generale',
    db_btn_cost: 'Costo umano della Guerra civile',
    db_btn_exili: 'Esiliati e deportati',
    db_btn_repres: 'Repressi della dittatura',

    hero_title: 'Spazio virtuale della Memoria Storica di Terrassa',
    hero_subtitle: 'Scopri le storie, i dati<br> e le testimonianze che compongono<br> la memoria storica.',
    hero_text: 'Lo Spazio Virtuale della Memoria Storica di Terrassa (EVMHT) è uno spazio storico, documentale, educativo e di ricerca che raccoglie il costo umano della lotta per la libertà dei cittadini di Terrassa tra il 1936 e il 1983.',
    hero_cta: 'Scopri di più sullo Spazio Virtuale',

    resources_title: 'Esplora le nostre risorse',
    resources_subtitle: 'Consulta e interagisci con banche dati che includono schede<br> individuali, documenti e studi storici',

    grid_title_general: 'Banca dati<br>Generale',
    grid_title_cost: 'Costo umano <br>della Guerra civile',
    grid_title_exili: 'Esiliati e<br> deportati',
    grid_title_repres: 'Repressi <br>della dittatura',

    interactive_title: 'Risorse interattive',
    interactive_item1: 'Storie personali delle<br> persone represse',
    interactive_item2: 'Strumenti di ricerca avanzata<br> per esplorare archivi',
    interactive_item3: 'Banche dati dettagliate<br> per tipo di repressione',
    interactive_item4: 'Fonti documentarie<br> verificate e accessibili',

    team_title: 'Team di ricerca e tecnico del sito',
    team_subtitle: 'Spazio Virtuale della Memoria Storica di Terrassa',
    team_text: "<span class='negreta'>Chi c’è dietro il progetto?</span> Questo progetto è possibile grazie alla collaborazione di enti e professionisti impegnati nel recupero della memoria storica",
    team_members: 'Membri della ricerca storica:',
  },
  pt: {
    basicSearch_titleA: 'Pesquisa básica de',
    basicSearch_titleB: 'pessoas reprimidas',
    basicSearch_placeholder: 'Nome e apelidos...',
    basicSearch_help: 'Se quiser fazer uma pesquisa avançada, aplicando filtros por local de nascimento, sexo, filiação sindical e/ou política, entre outros, clique no botão abaixo.',
    basicSearch_cta: 'Pesquisa avançada',

    explore_titleA: 'Explore as nossas',
    explore_titleB: 'bases de dados',
    explore_text: 'Consulte as bases de dados do Espaço Virtual da Memória Histórica de Terrassa, onde pode encontrar a lista completa de reprimidos e realizar pesquisas avançadas.',
    db_btn_general: 'Geral',
    db_btn_cost: 'Custo humano da Guerra Civil',
    db_btn_exili: 'Exilados e deportados',
    db_btn_repres: 'Reprimidos da ditadura',

    hero_title: 'Espaço virtual da Memória Histórica de Terrassa',
    hero_subtitle: 'Descubra as histórias, os dados<br> e os testemunhos que compõem<br> a memória histórica.',
    hero_text: 'O Espaço Virtual da Memória Histórica de Terrassa (EVMHT) é um espaço histórico, documental, educativo e de investigação que recolhe o custo humano da luta pela liberdade dos habitantes de Terrassa entre 1936 e 1983.',
    hero_cta: 'Saiba mais sobre o Espaço Virtual',

    resources_title: 'Explore os nossos recursos',
    resources_subtitle: 'Consulte e interaja com bases de dados que incluem fichas<br> individuais, documentos e estudos históricos',

    grid_title_general: 'Base de dados<br>Geral',
    grid_title_cost: 'Custo humano <br>da Guerra Civil',
    grid_title_exili: 'Exilados e<br> deportados',
    grid_title_repres: 'Reprimidos <br>da ditadura',

    interactive_title: 'Recursos interativos',
    interactive_item1: 'Histórias pessoais das<br> pessoas reprimidas',
    interactive_item2: 'Ferramentas de pesquisa avançada<br> para explorar arquivos',
    interactive_item3: 'Bases de dados detalhadas<br> por tipo de repressão',
    interactive_item4: 'Fontes documentais<br> verificadas e acessíveis',

    team_title: 'Equipa de investigadores e técnicos do site',
    team_subtitle: 'Espaço Virtual da Memória Histórica de Terrassa',
    team_text: "<span class='negreta'>Quem está por trás do projeto?</span> Este projeto é possível graças à colaboração de entidades e profissionais dedicados à recuperação da memória histórica",
    team_members: 'Membros da investigação histórica:',
  },
});
