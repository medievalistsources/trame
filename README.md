# TRAME
TRAME is a research infrastructure project for the development and interoperability of web databases about medieval manuscript tradition.

##1. What is it?
TRAME is a metasearch tool for medieval manuscripts, hosted by the Fondazione Ezio Franceschini and SISMEL. It aims to assist users in finding primary sources for the study of medieval culture. Originally developed in 2011 to allow a combined search on a group of different Italian repositories and databases, it has increasingly widened its scope to become a global research infrastructure for all scholars of the Middle Ages. Its main aim is to build a research infrastructure that focuses on promoting interoperability and fostering discoverability among the different digital resources available in the medieval digital ecosystem. Since 2014 TRAME has focused primarily on extending the meta-search approach to other web resources, using the user’s interaction with the research tool in an attempt to define a medieval manuscripts ontology, and redesigning the portal with the aim of improving the accessibility and usability of the site. Currently it implements a number of features (including simple search, shelf-mark search, and advanced search) on more than 80 selected scholarly digital resources devoted to western medieval manuscripts, authors, and texts across the EU and USA. These resources include digital libraries, research databases and other projects from leading research institutions. TRAME is a research tool rooted in the international medieval scholarly community and an ongoing collaborative international effort. Its development is in line with the Memorandum of Understanding of the COST Action IS1005 “Medieval Europe Medieval Cultures and Technological Resources”, representing 260 researchers coming from 39 leading institutions in 24 countries across the European Union.

[![ScreenShot](http://img.youtube.com/vi/_uXPH5WK9hU/0.jpg)](https://www.youtube.com/watch?v=_uXPH5WK9hU)

##2. What can I find there?
TRAME allows the user to search simultaneously in library catalogues, project databases and research portals. It combines online resources that are inside the TRAME network as well as external sites. The infrastructure combines both:

### Bibliographies pertaining to manuscripts, e.g.
- MEL – Medioevo latino. Bollettino bibliografico della cultura europea da Boezio e Erasmo (secoli VI-XV)
- MEM – Medioevo musicale. Bollettino bibliografico della musica medievale
- TLION mss – Bibliografia dei manoscritti citati in rivista
- BibMan – Bibliografia dei manoscritti in alfabeto latino conservati in Italia

### Repertories of texts and manuscripts, e.g.
- LIO – Repertorio della lirica italiana delle Origini
- BAI – Biblioteca Agiografica Italiana
- MAFRA – Repertorio dei manoscritti gallo-romanzi esemplati in Italia
- MAGIS – Manoscritti agiografici dell’Italia del Sud
- IRHT – Jonas : Répertoire des textes et des manuscrits médiévaux d’oc et d’oïl - JONAS
- Ramon Llull Database
- Repertorium Biblicum Medii Aevii
- BHL Biblioteca Hagiographica Latina manuscrita

### Catalogues of manuscripts (with images), e.g.
- CODEX – Inventario dei manoscritti medievali della Toscana
- MDI – Manoscritti datati d’Italia
- Manuscriptorium
- Enluminures

### Repertories pertaining to the history of traditions, e.g.
- TETRA – La trasmissione dei testi latini del Medioevo
- TLION – Tradizione della letteratura italiana online

### Bio-bibliographies
- BISLAM – Bibliotheca Scriptorum Latinorum Medii Recentiorisque Aevi
- CALMA – Compendium Auctorum Latinorum Medii Aevi (500-1500)

### Library catalogues, e.g.
- Beinecke Digital Collections
- Bodleian Library
- Bibliothèque nationale de France
- Trinity College
- Bayerische Staatsbibliothek
- Hill Museum & Manuscript Library
- Munchen (BSB) - MDZ
- Early Manuscripts at Oxford University
- Bibliothèque Municipale de Lyon
- Mazarinum - Les collections numeriques de la Bibliotheque Mazarine - Mazarinum

##3. Technical Background

TRAME’s development has been influenced by changes regarding the nature of information available in the www, TRAME has developed from a basic meta-search approach towards an attempt to establish a Medieval Semantic Knowledge base, by using custom applications for information collection and integration (i.e.: web crawler, data miner).

The application is written in OO-PHP, the design follows the MVC Pattern, the RDBMS is MySql and the front-end combines Xhtml and Javascript.

The search engine scans a set of sources for searched query terms and retrieves links to provide a wide range of information, including simple references, detailed manuscript record, and full-text transcriptions.

Currently, it is possible to perform queries by freetext, shelf-mark, author, title, date, copyist or incipit, on more than 80 selected scholarly digital resources across the EU and the USA.

Advantages of TRAME’s search of remote resources:

- TRAME has light and flexible infrastructure, as both data indexes are not stored in a central database. Actually no information is stored except for a few technical metadata.
- TRAME will send the user query across a vast number of repositories and present the results in a single list.
- TRAME can send a user query across a number or remote systems over HTTP protocol, it’s also supporting OAI-PMH on selected repositories and (if available) specific APIs
- The results will be divided in groups according to their provenance or type (the original data provider)
- All search results found by TRAME’s meta-search engine are accessible via the original provider’s web site, with their own policies and licensing methods
- A user query is sent simultaneously over a wide number of connected systems in order to collect a unique list of results. The search results will have all the information needed to identify each individual manuscript, such as localization (City, Library and Holding), shelf-mark and the link to the actual digital resource (URI: uniform resource identifier)

##4. How to install
- Create a virtual directory.
- Save "rest.php", "common.inc.php" and "client.inc.php" files in the virtual directory.
- Save "Appl" and "Client" folder in the virtual directory.
- Create a DB with the information about the platform/sites to search on; "tramenew.dump" is a dump of an example DB.
- "common.inc.php" and "client.inc.php" files must be modified with your DB parameters.
