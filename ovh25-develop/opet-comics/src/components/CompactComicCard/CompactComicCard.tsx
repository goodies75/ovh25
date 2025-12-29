import { Card } from '../ui';
import './CompactComicCard.css';

interface Fiche {
  id: number;
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
  image_url?: string;
  created_at: string;
}

interface CompactComicCardProps {
  fiche: Fiche;
  onClick: (fiche: Fiche) => void;
}

export default function CompactComicCard({ fiche, onClick }: CompactComicCardProps) {
  return (
    <Card className="compact-comic-card" onClick={() => onClick(fiche)}>
      <div className="compact-card-content">
        <div className="compact-card-main">
          <h3 className="compact-title">{fiche.nom_serie}</h3>
          {fiche.numero && (
            <span className="compact-number">#{fiche.numero}</span>
          )}
        </div>
        
        <div className="compact-card-meta">
          <span className="compact-publisher">{fiche.editeur}</span>
          {fiche.annee && (
            <span className="compact-year">({fiche.annee})</span>
          )}
        </div>
        
        {fiche.image_url && (
          <div className="compact-image">
            <img src={fiche.image_url} alt={fiche.nom_serie} />
          </div>
        )}
      </div>
      
      <div className="compact-card-indicator">
        <span>👁️ Voir détails</span>
      </div>
    </Card>
  );
}
