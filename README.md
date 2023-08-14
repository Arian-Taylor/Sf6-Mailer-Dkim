# Symfony6 - Mailer - Dkim
Mise en application d'un projet Symfony6 avec Mailer et la signature DKIM

## Requirement
- PHP version:
	- `>=8.1`

## Installation
- Installing dependancies :
	- `php composer.phar install`

- Configuration VE in the **.env** file :
	- `MAILER_DSN` :  configuration de la connexion à un service de messagerie (SMTP, Sendmail, etc.) afin d'envoyer des e-mails à partir de l'application.
	- `SYS_DKIM_SIGNEE_ACTIVE` : configuration d'activation ou désactivation du signature DKIM, valeur possible `"on"` ou `"off"`
	- `SYS_DKIM_SIGNEE_DOMAIN` : nom de domaine du site pour la signature
	- `SYS_DKIM_SIGNEE_SELECTOR` : selecteur de votre domaine pour la signature
	- `SYS_DKIM_FILE_NAME` : nom du fichier de signature, par défaut `"privatekey.pem"`
	- `SYS_DKIM_PATH_ABSOLUTE` : si le chemin vers le fichier de signature est absolute, valeur possible `"on"` ou `"off"`
	- `MAILER_FROM` : adresse de l'expediteur
	- `MAILER_NAME` : nom de l'expediteur
	- `APP_URL` : url du site

- Generer et copier la clé privé dkim du projet dans `Sf6-Mailer-Dkim\private\dkim\` si vous avez activer la signature DKIM `SYS_DKIM_SIGNEE_ACTIVE="on"`

- Starting the server :
	- `symfony serve --port=8000`

## Testing URLs
- https://127.0.0.1:8000/test/send/email
- https://127.0.0.1:8000/test/send/email/with/attachements