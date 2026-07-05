# Pronote

Application web de gestion de notes scolaires, inspirée du logiciel Pronote utilisé
dans les établissements français. Développée avec **Symfony 8.1** (API backend) et
**React** (frontend SPA).

## Fonctionnalités

Page de connexion:

<img width="1440" height="900" alt="Capture d’écran 2026-07-05 à 11 36 00" src="https://github.com/user-attachments/assets/ab9d8cb2-848f-4cc2-b8b4-029345e43e7b" />

**Professeur**
- Consulter la liste de ses classes et matières attribuées
- Créer un examen pour une classe (intitulé + date)
- Modifier un examen existant
- Saisir et modifier les notes des élèves pour un examen
- Consulter les notes et la moyenne de classe par examen

Liste des classes et des examens crées, avec pagination par classe :

<img width="1440" height="900" alt="Capture d’écran 2026-07-05 à 11 36 41" src="https://github.com/user-attachments/assets/dc4b6a17-5d60-4d09-b444-f7a0df02343c" />

Page de création d'un examen : 

<img width="1440" height="900" alt="Capture d’écran 2026-07-05 à 11 36 50" src="https://github.com/user-attachments/assets/10f160b5-6716-4b32-87f5-7c0944ee9e26" />

Page pour enregistrer les notes de l'examen :

<img width="1440" height="900" alt="Capture d’écran 2026-07-05 à 11 37 04" src="https://github.com/user-attachments/assets/ae10eee6-cc7c-47d2-8b5f-07ed807a7dbb" />

**Élève**
- Consulter ses notes, organisées par matière
- Voir sa moyenne par matière et sa moyenne générale
- Se situer par rapport à la moyenne de la classe sur chaque examen
- Visualiser ses résultats sous forme de diagramme radar (comparaison avec la classe)
  
Graphique des moyennes de l'élève et de la classe par matière :

<img width="1440" height="900" alt="Capture d’écran 2026-07-05 à 11 35 34" src="https://github.com/user-attachments/assets/8c776d77-6567-4598-8a4f-3dd099a37dd0" />

Note et moyenne de l'élève par matière et moyenne de classe pour chaque examen :

<img width="1440" height="900" alt="Capture d’écran 2026-07-05 à 11 35 51" src="https://github.com/user-attachments/assets/c3695fb3-4f74-4369-9bee-c1342b232e55" />

## Stack technique

| Composant | Technologie |
|---|---|
| Backend | Symfony 8.1 (PHP 8.4+) |
| Base de données | MySQL / MariaDB |
| Authentification | JWT (LexikJWTAuthenticationBundle) |
| Frontend | React (Vite) |
| UI | Bootstrap 5 |
| Graphiques | Chart.js (react-chartjs-2) |

### Prérequis

- PHP 8.4 ou supérieur
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)
- Node.js 18+ et npm
- MySQL/MariaDB (via [MAMP](https://www.mamp.info/), XAMPP, ou installation native)

