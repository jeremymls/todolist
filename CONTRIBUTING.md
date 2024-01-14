# Contribuer à To Do List

## Documentation de l'Implémentation de l'Authentification

### Fichiers à Modifier

- **`security.yaml` :**
  - Contient la configuration de sécurité de l'application.
  - Définit les paramètres d'authentification.

### Processus d'Authentification

- L'authentification est basée sur un formulaire de connexion.
- Symfony vérifie les informations fournies et authentifie l'utilisateur si les identifiants sont valides.
- Le formulaire est géré par `App\Security\ToDoListAuthenticator`.
  - Implémente l'interface `Symfony\Component\Security\Guard\AuthenticatorInterface`.
  - Contient la logique d'authentification personnalisée.
- Redirection :
  - Réussite : Page d'accueil.
  - Échec : Page de connexion avec un message d'erreur.

### Stockage des Utilisateurs

- Les utilisateurs sont stockés dans la base de données.
- `App\Entity\User` représente l'entité utilisateur, mappée à la table correspondante.
- La propriété `username` est utilisée comme identifiant unique.

## Collaboration et Processus de Modification

1. **Clonez le dépôt Git :**
   - `git clone https://url_du_depot.git`

2. **Créez une Nouvelle Branche :**
   - `git checkout -b nom_de_la_branche`

3. **Effectuez les Modifications :**
   - Modifiez les fichiers nécessaires.

4. **Testez Localement :**
   - Assurez-vous que les modifications fonctionnent correctement.

5. **Commit et Push :**
   - `git commit -m "Description de la modification"`
   - `git push origin nom_de_la_branche`

6. **Pull Request :**
   - Créez une pull request pour intégrer vos modifications.

## Qualité et Règles à Respecter

1. **Règles de Codage :**
   - Symfony 6: [Conventions Symfony](https://symfony.com/doc/current/contributing/code/standards.html)
   - Normes PSR 1, 2 et 12.
   - Utilisez des noms de variables et de fonctions significatifs.

2. **Tests Unitaires :**
   - Écrivez des tests unitaires exhaustifs.

3. **Revues de Code :**
   - Avant la fusion, demandez une revue de code.

4. **Documentation :**
   - Documentez vos modifications.
   - Ajoutez des commentaires pertinents dans le code.

5. **Sécurité :**
   - Évitez les injections SQL et respectez les bonnes pratiques de sécurité.

## Rapport de Bugs et Demandes de Fonctionnalités

- Créez une issue pour décrire le problème ou la proposition.

## Licence

En contribuant, vous acceptez la licence du projet.

Merci de contribuer à To Do List! Si vous avez des questions, n'hésitez pas à demander.
