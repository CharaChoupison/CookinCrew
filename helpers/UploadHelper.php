<?php
namespace Helpers;

class UploadHelper
{
    /**
     * Gère l'upload d'un fichier via un champ <input type="file" name="$inputName">
     * et le stocke dans /uploads/$subFolder/.
     *
     * @param string $inputName Le name du champ input (ex: 'image')
     * @param string $subFolder Le sous-dossier dans "uploads/" (ex: 'posts', 'profile-pic', etc.)
     * @return string|null Chemin relatif pour afficher le fichier (ex: "uploads/posts/monfichier.jpg"),
     *                     ou null si aucun fichier ou erreur.
     */
    public static function handleUpload(string $inputName, string $subFolder = 'posts'): ?string
    {
        if (!empty($_FILES[$inputName]['name'])) {
            // Nom unique
            $fileName = time() . '_' . basename($_FILES[$inputName]['name']);

            // Chemin absolu vers uploads/$subFolder
            $targetDir = __DIR__ . '/../../uploads/' . $subFolder . '/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Fichier final
            $targetFile = $targetDir . $fileName;

            // Déplacer
            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetFile)) {
                // On renvoie un chemin relatif à insérer en base et à afficher (ex: "uploads/posts/xxx.jpg")
                return 'uploads/' . $subFolder . '/' . $fileName;
            }
        }

        return null; // pas de fichier
    }
}
