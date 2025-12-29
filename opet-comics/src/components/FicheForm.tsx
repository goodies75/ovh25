import { useState } from "react";
import { Button, Card, Input } from "./ui";
import PhotoCapture from "./PhotoCapture";

interface Fiche {
  nom_serie: string;
  numero: string;
  annee: string;
  numero_edition: string;
  editeur: string;
  auteur_couverture: string;
  autres_auteurs: string[];
  titre_secondaire: string;
  etat: string;
  isbn: string;
  description: string;
  image_url: string;
}

export default function FicheForm() {
  const [fiche, setFiche] = useState<Fiche>({
    nom_serie: "",
    numero: "",
    annee: "",
    numero_edition: "",
    editeur: "",
    auteur_couverture: "",
    autres_auteurs: [],
    titre_secondaire: "",
    etat: "Très bon",
    isbn: "",
    description: "",
    image_url: ""
  });

  const [nouvelAuteur, setNouvelAuteur] = useState("");
  const [showPhotoCapture, setShowPhotoCapture] = useState(false);
  const [isUploadingImage, setIsUploadingImage] = useState(false);

  const etats = ["Neuf", "Très bon", "Bon", "Moyen", "Abîmé"];

  const ajouterAuteur = () => {
    if (nouvelAuteur.trim() && !fiche.autres_auteurs.includes(nouvelAuteur.trim())) {
      setFiche({
        ...fiche,
        autres_auteurs: [...fiche.autres_auteurs, nouvelAuteur.trim()]
      });
      setNouvelAuteur("");
    }
  };

  const supprimerAuteur = (index: number) => {
    setFiche({
      ...fiche,
      autres_auteurs: fiche.autres_auteurs.filter((_, i) => i !== index)
    });
  };

  // ========== GESTION DES PHOTOS ==========
  const handlePhotoCapture = async (imageData: string, fileName: string) => {
    setIsUploadingImage(true);

    try {
      let result;

      // En développement local, utiliser le mock
      if (window.location.hostname === 'localhost' && (window as any).mockUpload) {
        console.log('Mode développement: utilisation du mock upload');
        result = await (window as any).mockUpload(imageData, fileName);
      } else {
        // En production, utiliser l'API réelle
        const formData = new FormData();
        formData.append('image', imageData); // 'image' attendu par upload-image.php
        formData.append('filename', fileName);

        const response = await fetch('./upload-image.php', {
          method: 'POST',
          body: formData
        });

        result = await response.json();
      }

      if (result.success && result.url) {
        setFiche({
          ...fiche,
          image_url: result.url
        });
        alert('Photo ajoutée avec succès !');
      } else {
        throw new Error(result.error || 'Erreur upload');
      }
    } catch (error) {
      console.error('Erreur upload photo:', error);
      alert('Erreur lors de l\'upload de la photo');
    } finally {
      setIsUploadingImage(false);
      setShowPhotoCapture(false);
    }
  };

  const openPhotoCapture = () => {
    setShowPhotoCapture(true);
  };

  const closePhotoCapture = () => {
    setShowPhotoCapture(false);
  };

  const removeImage = () => {
    setFiche({
      ...fiche,
      image_url: ""
    });
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    try {
      const response = await fetch("./post-fiche.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(fiche),
      });

      const result = await response.json();
      console.log("Résultat API :", result);

      if (result.success) {
        alert("Comic ajouté !");
        setFiche({
          nom_serie: "",
          numero: "",
          annee: "",
          numero_edition: "",
          editeur: "",
          auteur_couverture: "",
          autres_auteurs: [],
          titre_secondaire: "",
          etat: "Très bon",
          isbn: "",
          description: "",
          image_url: ""
        });
        window.dispatchEvent(new CustomEvent('ficheAdded'));
      } else {
        alert("Erreur : " + (result.error || "Erreur inconnue"));
      }
    } catch (error) {
      console.error("Erreur lors de l'ajout :", error);
      alert("Erreur de connexion");
    }
  };

  return (
    <Card className="form-container">
      <form onSubmit={handleSubmit} className="comic-form">

        {/* Section principale */}
        <div className="form-section">
          <h3>Informations principales</h3>

          <div className="form-row">
            <Input
              label="Nom de série"
              placeholder="Nom de série *"
              value={fiche.nom_serie}
              onChange={(value) => setFiche({ ...fiche, nom_serie: value })}
              required
            />
            <Input
              label="Numéro"
              placeholder="Numéro"
              value={fiche.numero}
              onChange={(value) => setFiche({ ...fiche, numero: value })}
            />
          </div>

          <div className="form-row">
            <Input
              label="Année"
              placeholder="Année"
              type="number"
              value={fiche.annee}
              onChange={(value) => setFiche({ ...fiche, annee: value })}
            />
            <Input
              label="Numéro d'édition"
              placeholder="Numéro d'édition"
              value={fiche.numero_edition}
              onChange={(value) => setFiche({ ...fiche, numero_edition: value })}
            />
          </div>

          <div className="form-group">
            <input
              className="form-input"
              placeholder="Éditeur"
              value={fiche.editeur}
              onChange={(e) => setFiche({ ...fiche, editeur: e.target.value })}
            />
          </div>

          <div className="form-group">
            <input
              className="form-input"
              placeholder="Titre secondaire"
              value={fiche.titre_secondaire}
              onChange={(e) => setFiche({ ...fiche, titre_secondaire: e.target.value })}
            />
          </div>
        </div>

        {/* Section auteurs */}
        <div className="form-section">
          <h3>Auteurs</h3>

          <div className="form-group">
            <input
              className="form-input"
              placeholder="Auteur de la couverture"
              value={fiche.auteur_couverture}
              onChange={(e) => setFiche({ ...fiche, auteur_couverture: e.target.value })}
            />
          </div>

          <div className="form-group">
            <div className="auteurs-input">
              <input
                className="form-input"
                placeholder="Autres auteurs"
                value={nouvelAuteur}
                onChange={(e) => setNouvelAuteur(e.target.value)}
                onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), ajouterAuteur())}
              />
              <button type="button" onClick={ajouterAuteur} className="btn-add-author">
                ➕
              </button>
            </div>

            {fiche.autres_auteurs.length > 0 && (
              <div className="auteurs-list">
                {fiche.autres_auteurs.map((auteur, index) => (
                  <span key={index} className="auteur-tag">
                    {auteur}
                    <button
                      type="button"
                      onClick={() => supprimerAuteur(index)}
                      className="btn-remove-author"
                    >
                      ❌
                    </button>
                  </span>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Section détails */}
        <div className="form-section">
          <h3>📋 Détails</h3>

          <div className="form-row">
            <div className="form-group">
              <select
                className="form-input"
                value={fiche.etat}
                onChange={(e) => setFiche({ ...fiche, etat: e.target.value })}
              >
                {etats.map(etat => (
                  <option key={etat} value={etat}>{etat}</option>
                ))}
              </select>
            </div>
            <div className="form-group">
              <input
                className="form-input"
                placeholder="ISBN"
                value={fiche.isbn}
                onChange={(e) => setFiche({ ...fiche, isbn: e.target.value })}
              />
            </div>
          </div>

          {/* ========== SECTION PHOTO DE COUVERTURE ========== */}
          <div className="form-group">
            <label className="form-label">📸 Photo de Couverture</label>

            {/* Aperçu de l'image actuelle */}
            {fiche.image_url && (
              <div className="image-preview-container">
                <img
                  src={fiche.image_url}
                  alt="Aperçu couverture"
                  className="image-preview"
                />
                <button
                  type="button"
                  onClick={removeImage}
                  className="remove-image-btn"
                  title="Supprimer l'image"
                >
                  🗑️
                </button>
              </div>
            )}

            {/* Boutons d'action photo */}
            <div className="photo-actions">
              <button
                type="button"
                onClick={openPhotoCapture}
                className="photo-capture-btn"
                disabled={isUploadingImage}
              >
                {isUploadingImage ? '⏳ Upload...' : '📷 Prendre/Choisir Photo'}
              </button>

              <div className="divider-text">ou</div>

              {/* Input URL manuel (pour compatibilité) */}
              <input
                className="form-input url-input"
                placeholder="URL de l'image (optionnel)"
                value={fiche.image_url}
                onChange={(e) => setFiche({ ...fiche, image_url: e.target.value })}
              />
            </div>
          </div>

          <div className="form-group">
            <textarea
              className="form-textarea"
              placeholder="Description"
              value={fiche.description}
              onChange={(e) => setFiche({ ...fiche, description: e.target.value })}
              rows={4}
            />
          </div>
        </div>

        <Button type="submit" className="btn--full-width">
          ➕ Ajouter le Comic
        </Button>
      </form>

      {/* ========== MODAL DE PRISE DE PHOTO ========== */}
      {showPhotoCapture && (
        <PhotoCapture
          onPhotoCapture={handlePhotoCapture}
          onCancel={closePhotoCapture}
        />
      )}
    </Card>
  );
}
