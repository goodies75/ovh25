import { useEffect, useState } from "react";
import { Library, Search } from 'lucide-react';
import { Button } from "../ui";
import ComicCompactCard from '../ComicCompactCard';
import '../ComicCompactCard.css';
import ComicDetailModal from '../ComicDetailModal';
import '../ComicDetailModal.css';
import ComicEditModal from '../ComicEditModal';
import PinProtection from '../security/PinProtection';
import { useAdminAuth } from '../../hooks/useAdminAuth';
import Modal from '../Modal/Modal';
import './ListPage.css';

interface Fiche {
  id: number;
  nom_serie: string;
  titre?: string; // Ajout du champ titre optionnel
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
  image_url?: string;
  created_at: string;
}

type SortOption = 'nom_serie' | 'annee' | 'editeur' | 'date_added';
type SortDirection = 'asc' | 'desc';

export default function ListPage() {
  const [fiches, setFiches] = useState<Fiche[]>([]);
  const [sortedFiches, setSortedFiches] = useState<Fiche[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  // Hook de sécurité
  const { isAuthorized, authorize } = useAdminAuth();
  
  // États pour les modales
  const [selectedFiche, setSelectedFiche] = useState<Fiche | null>(null);
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [ficheToDelete, setFicheToDelete] = useState<Fiche | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);
  
  // États pour la sécurité
  const [showPinModal, setShowPinModal] = useState(false);
  const [pendingAction, setPendingAction] = useState<'edit' | 'delete' | null>(null);
  const [pendingFiche, setPendingFiche] = useState<Fiche | null>(null);
  
  // États pour le tri
  const [sortBy, setSortBy] = useState<SortOption>('nom_serie');
  const [sortDirection, setSortDirection] = useState<SortDirection>('asc');
  const [searchTerm, setSearchTerm] = useState('');

  const fetchFiches = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await fetch("./get-fiches.php");
      if (!response.ok) {
        throw new Error(`Erreur HTTP: ${response.status}`);
      }
      const data = await response.json();
      
      // Normaliser les données pour compatibilité - préserver les données originales
      const normalizedFiches = data.map((fiche: any) => ({
        ...fiche, // Préserver toutes les données originales
        id: fiche.id,
        nom_serie: fiche.nom_serie || fiche.titre || "Sans titre",
        titre: fiche.titre || fiche.nom_serie || "Sans titre", // Garder titre pour compatibilité
        numero: fiche.numero || "",
        annee: fiche.annee || "",
        numero_edition: fiche.numero_edition || "",
        editeur: fiche.editeur || "",
        auteur_couverture: fiche.auteur_couverture || "",
        autres_auteurs: Array.isArray(fiche.autres_auteurs) ? fiche.autres_auteurs : [],
        titre_secondaire: fiche.titre_secondaire || "",
        etat: fiche.etat || "Non défini",
        isbn: fiche.isbn || "",
        description: fiche.description || "",
        image_url: fiche.image_url || "",
        created_at: fiche.created_at
      }));
      
      setFiches(normalizedFiches);
    } catch (err) {
      console.error("Erreur lors du fetch :", err);
      setError("Impossible de charger les fiches");
    } finally {
      setLoading(false);
    }
  };

  // Fonction de tri
  const sortFiches = (fiches: Fiche[], sortBy: SortOption, direction: SortDirection, searchTerm: string) => {
    let filtered = fiches;
    
    // Filtrage par recherche
    if (searchTerm) {
      filtered = fiches.filter(fiche =>
        fiche.nom_serie.toLowerCase().includes(searchTerm.toLowerCase()) ||
        fiche.editeur.toLowerCase().includes(searchTerm.toLowerCase()) ||
        fiche.auteur_couverture.toLowerCase().includes(searchTerm.toLowerCase()) ||
        fiche.autres_auteurs.some(auteur => auteur.toLowerCase().includes(searchTerm.toLowerCase()))
      );
    }
    
    // Tri
    const sorted = [...filtered].sort((a, b) => {
      let valueA: string | number = '';
      let valueB: string | number = '';
      
      switch (sortBy) {
        case 'nom_serie':
          valueA = a.nom_serie.toLowerCase();
          valueB = b.nom_serie.toLowerCase();
          break;
        case 'annee':
          valueA = parseInt(a.annee) || 0;
          valueB = parseInt(b.annee) || 0;
          break;
        case 'editeur':
          valueA = a.editeur.toLowerCase();
          valueB = b.editeur.toLowerCase();
          break;
        case 'date_added':
          valueA = new Date(a.created_at).getTime();
          valueB = new Date(b.created_at).getTime();
          break;
      }
      
      if (direction === 'asc') {
        return valueA < valueB ? -1 : valueA > valueB ? 1 : 0;
      } else {
        return valueA > valueB ? -1 : valueA < valueB ? 1 : 0;
      }
    });
    
    return sorted;
  };

  // Effect pour mettre à jour le tri
  useEffect(() => {
    const sorted = sortFiches(fiches, sortBy, sortDirection, searchTerm);
    setSortedFiches(sorted);
  }, [fiches, sortBy, sortDirection, searchTerm]);

  useEffect(() => {
    fetchFiches();
  }, []);

  // Fonctions de sécurité
  const requireAuth = (action: 'edit' | 'delete', fiche: Fiche) => {
    if (isAuthorized) {
      // L'utilisateur est déjà autorisé, exécuter l'action directement
      if (action === 'edit') {
        openEditModal(fiche);
      } else {
        setFicheToDelete(fiche);
      }
    } else {
      // Demander l'autorisation
      setPendingAction(action);
      setPendingFiche(fiche);
      setShowPinModal(true);
    }
  };

  const handlePinSuccess = () => {
    authorize(); // Mettre à jour l'état d'autorisation
    setShowPinModal(false);
    
    // Exécuter l'action en attente
    if (pendingAction === 'edit' && pendingFiche) {
      openEditModal(pendingFiche);
    } else if (pendingAction === 'delete' && pendingFiche) {
      setFicheToDelete(pendingFiche);
    }
    
    // Nettoyer les états temporaires
    setPendingAction(null);
    setPendingFiche(null);
  };

  const handlePinCancel = () => {
    setShowPinModal(false);
    setPendingAction(null);
    setPendingFiche(null);
  };

  const openEditModal = (fiche: Fiche) => {
    setSelectedFiche(fiche);
    setShowEditModal(true);
  };

  const handleDeleteConfirm = async () => {
    if (!ficheToDelete) return;
    
    try {
      setIsDeleting(true);
      
      const response = await fetch('./delete-fiche.php', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: ficheToDelete.id })
      });

      if (response.ok) {
        setFiches(fiches.filter(f => f.id !== ficheToDelete.id));
        setFicheToDelete(null);
        console.log('Fiche supprimée avec succès');
      } else {
        throw new Error('Erreur lors de la suppression');
      }
    } catch (err) {
      console.error('Erreur lors de la suppression :', err);
      setError('Impossible de supprimer la fiche');
    } finally {
      setIsDeleting(false);
    }
  };

  const handleDeleteCancel = () => {
    setFicheToDelete(null);
  };

  const handleCloseEditModal = () => {
    setShowEditModal(false);
    setSelectedFiche(null);
  };

  const handleSaveEdit = async (updatedFiche: Fiche) => {
    try {
      console.log('Sauvegarde de:', updatedFiche);
      
      // Appel à l'API de mise à jour
      const response = await fetch('./update-fiche.php', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(updatedFiche),
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Mettre à jour la liste locale
        setFiches(fiches.map(f => f.id === updatedFiche.id ? updatedFiche : f));
        setShowEditModal(false);
        alert('Comic mis à jour avec succès !');
        
        // Rafraîchir la liste depuis le serveur pour être sûr
        fetchFiches();
      } else {
        throw new Error(result.error || 'Erreur lors de la sauvegarde');
      }
      
    } catch (error) {
      console.error('Erreur lors de la sauvegarde:', error);
      alert('Erreur lors de la sauvegarde: ' + (error as Error).message);
    }
  };

  const handleSortChange = (newSortBy: SortOption) => {
    if (newSortBy === sortBy) {
      // Si on clique sur la même colonne, inverser la direction
      setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
    } else {
      setSortBy(newSortBy);
      setSortDirection('asc');
    }
  };

  // Remplacer l'affichage des fiches par les cartes compactes
  return (
    <div className="list-page">
      <div className="list-header">
        <h2 style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
          <Library size={28} />
          <span>Collection de Comics</span>
        </h2>
        <p>Explorez et gérez votre collection complète</p>
      </div>

      {/* Contrôles de tri et recherche */}
      <div className="list-controls">
        <div className="search-section">
          <div style={{ position: 'relative', width: '100%' }}>
            <Search size={20} style={{ position: 'absolute', left: '12px', top: '50%', transform: 'translateY(-50%)', color: '#9ca3af' }} />
            <input
              type="text"
              placeholder="Rechercher par titre, éditeur, auteur..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="search-input"
              style={{ paddingLeft: '40px' }}
            />
          </div>
        </div>
        
        <div className="sort-section">
          <span className="sort-label">Trier par :</span>
          <div className="sort-buttons">
            <button
              className={`sort-btn ${sortBy === 'nom_serie' ? 'sort-btn--active' : ''}`}
              onClick={() => handleSortChange('nom_serie')}
            >
              Titre {sortBy === 'nom_serie' && (sortDirection === 'asc' ? '↑' : '↓')}
            </button>
            <button
              className={`sort-btn ${sortBy === 'annee' ? 'sort-btn--active' : ''}`}
              onClick={() => handleSortChange('annee')}
            >
              Année {sortBy === 'annee' && (sortDirection === 'asc' ? '↑' : '↓')}
            </button>
            <button
              className={`sort-btn ${sortBy === 'editeur' ? 'sort-btn--active' : ''}`}
              onClick={() => handleSortChange('editeur')}
            >
              Éditeur {sortBy === 'editeur' && (sortDirection === 'asc' ? '↑' : '↓')}
            </button>
            <button
              className={`sort-btn ${sortBy === 'date_added' ? 'sort-btn--active' : ''}`}
              onClick={() => handleSortChange('date_added')}
            >
              Date d'ajout {sortBy === 'date_added' && (sortDirection === 'asc' ? '↑' : '↓')}
            </button>
          </div>
        </div>
      </div>

      {/* Indicateur de résultats */}
      {!loading && !error && (
        <div className="results-info">
          <span>{sortedFiches.length} comic(s) trouvé(s)</span>
          {searchTerm && <span> pour "{searchTerm}"</span>}
        </div>
      )}
      
      {loading && (
        <div className="loading">
          <div className="spinner"></div>
          <p>Chargement de votre collection...</p>
        </div>
      )}
      
      {error && (
        <div className="error">
          {error}
        </div>
      )}
      
      {!loading && !error && sortedFiches.length === 0 && !searchTerm && (
        <div className="empty-state">
          <p>Aucune fiche dans votre collection</p>
          <small>Commencez par ajouter votre premier comic !</small>
        </div>
      )}

      {!loading && !error && sortedFiches.length === 0 && searchTerm && (
        <div className="empty-state">
          <p>Aucun résultat pour "{searchTerm}"</p>
          <small>Essayez avec d'autres mots-clés</small>
        </div>
      )}
      
      {!loading && !error && sortedFiches.length > 0 && (
        <div className="fiche-list">
          {sortedFiches.map((fiche) => (
            <ComicCompactCard
              key={fiche.id}
              fiche={fiche}
              onClick={() => {
                setSelectedFiche(fiche);
                setShowDetailModal(true);
              }}
            />
          ))}
        </div>
      )}

      {/* Modal de suppression */}
      <Modal
        isOpen={!!ficheToDelete}
        onClose={handleDeleteCancel}
        title="Confirmer la suppression"
        actions={
          <>
            <Button 
              variant="danger"
              onClick={handleDeleteConfirm} 
              disabled={isDeleting}
            >
              {isDeleting ? 'Suppression...' : 'Oui, supprimer'}
            </Button>
            <Button 
              variant="cancel"
              onClick={handleDeleteCancel} 
              disabled={isDeleting}
            >
              Annuler
            </Button>
          </>
        }
      >
        <p>Êtes-vous sûr de vouloir supprimer le comic "<strong>{ficheToDelete?.nom_serie} {ficheToDelete?.numero}</strong>" ?</p>
        <p className="modal-warning">Cette action est irréversible.</p>
      </Modal>

      {/* Modal de détail */}
      {showDetailModal && selectedFiche && (
        <ComicDetailModal
          isOpen={showDetailModal}
          onClose={() => setShowDetailModal(false)}
          fiche={selectedFiche}
          onEdit={() => {
            setShowDetailModal(false);
            requireAuth('edit', selectedFiche);
          }}
          onDelete={() => {
            setShowDetailModal(false);
            requireAuth('delete', selectedFiche);
          }}
        />
      )}

      {/* Modal d'édition */}
      {showEditModal && selectedFiche && (
        <ComicEditModal
          isOpen={showEditModal}
          fiche={selectedFiche}
          onClose={handleCloseEditModal}
          onSave={handleSaveEdit}
        />
      )}

      {/* Modal de protection PIN */}
      <PinProtection
        isOpen={showPinModal}
        onClose={handlePinCancel}
        onSuccess={handlePinSuccess}
        action={pendingAction === 'edit' ? 'modifier' : 'supprimer'}
        comicTitle={pendingFiche ? (pendingFiche.nom_serie || pendingFiche.titre) : undefined}
      />
    </div>
  );
}
