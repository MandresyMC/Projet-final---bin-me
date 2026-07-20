# Tâches réalisées — v2

## Transfert : option "frais de retrait"

- Ajout d'une case à cocher "Ajouter les frais de retrait" sur le formulaire de transfert.
- Disponible uniquement quand tous les numéros destinataires saisis sont des numéros locaux (MVola) ; désactivée automatiquement en JS dès qu'un numéro d'un autre opérateur est détecté, et ignorée côté serveur dans tous les cas si le groupe n'est pas "local".
- Quand elle est cochée : le montant crédité à chaque destinataire est majoré du frais de retrait (barème `retrait`) correspondant à sa part, pour qu'il puisse retirer sans frais à sa charge. Le frais local (barème `transfert`) reste inchangé.

## Envoi multiple (plusieurs destinataires)

- Un seul groupe d'opérateurs autorisé par envoi : soit tous les numéros sont locaux, soit tous sont d'un autre opérateur — le mélange est rejeté avec un message d'erreur explicite.
- Le montant saisi est réparti à parts égales entre les destinataires ; le frais d'envoi est calculé une seule fois sur le montant total (pas par destinataire).
- La commission (transferts vers un autre opérateur) est calculée par destinataire, sur sa part du montant.

## Corrections de bugs

- `OperationController::createOperation()` (transfert) : `id_user_destination` était inséré comme tableau au lieu d'un entier ; le frais et la commission d'un destinataire étaient écrasés par ceux du dernier numéro de la boucle ; `numero_destination` n'était jamais enregistré en base.
- Transfert vers un numéro local sans compte MVola : débitait l'expéditeur sans jamais créditer personne (argent perdu). Rejeté maintenant avec un message d'erreur avant tout débit.
- Historique client (`historiques()`) : le numéro du destinataire s'affichait à `-` pour tout transfert vers un autre opérateur (jointure sur le compte MVola uniquement, jamais sur `numero_destination`). Corrigé avec `COALESCE(dst.numero_telephone, operation.numero_destination)`, comme c'était déjà le cas côté admin.
- Migration `AddNumeroDestinationToOperation` jamais appliquée sur la base locale (`writable/fitspace.db`) alors que le code (et `base.sql`) supposaient déjà la colonne présente — appliquée.

## UI "Ajouter un numéro"

- Le formatage en direct du numéro (groupes de chiffres) ne s'appliquait qu'au premier champ ; il s'applique maintenant aussi aux numéros ajoutés dynamiquement.
- Ajout d'un style cohérent avec le reste du formulaire pour les boutons "+ Ajouter un numéro", "Supprimer" et la case "frais de retrait" (bordures/ombres du design existant), à la place des boutons non stylés.

## base.sql

Déjà à jour avec la colonne `numero_destination` sur `operation` — aucune modification nécessaire, seule la migration devait encore être appliquée en local (voir ci-dessus).
