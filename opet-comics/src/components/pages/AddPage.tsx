import FicheForm from '../FicheForm';
import { Card } from '../ui';

export default function AddPage() {
  return (
    <div className="min-h-screen py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="text-center mb-12">
          <h2 className="text-4xl sm:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-primary-600 to-teal-600 bg-clip-text text-transparent">
            ➕ Ajouter un Comic
          </h2>
          <p className="text-lg text-dark-600">
            Enrichissez votre collection en ajoutant un nouveau comic avec toutes ses informations
          </p>
        </div>

        {/* Formulaire */}
        <div className="mb-12">
          <FicheForm />
        </div>

        {/* Conseils */}
        <Card glass className="p-8">
          <h3 className="text-2xl font-display font-bold mb-6 text-dark-800 flex items-center gap-3">
            <span className="text-3xl">💡</span>
            Conseils pour un ajout optimal
          </h3>
          <ul className="space-y-4 text-dark-700">
            {[
              { icon: '📖', label: 'Titre :', desc: 'Utilisez le nom exact de la série' },
              { icon: '🔢', label: 'Numéro :', desc: 'Indiquez le numéro du tome ou de l\'épisode' },
              { icon: '📅', label: 'Année :', desc: 'Année de publication originale' },
              { icon: '🏢', label: 'Éditeur :', desc: 'Nom de la maison d\'édition' },
              { icon: '🎨', label: 'Auteurs :', desc: 'Ajoutez tous les contributeurs importants' },
              { icon: '🖼️', label: 'Image :', desc: 'URL d\'une image de couverture de qualité' },
            ].map((tip, index) => (
              <li key={index} className="flex items-start gap-3 p-3 rounded-lg hover:bg-white/50 transition-colors duration-300">
                <span className="text-2xl flex-shrink-0">{tip.icon}</span>
                <div>
                  <strong className="text-dark-800">{tip.label}</strong>{' '}
                  <span className="text-dark-600">{tip.desc}</span>
                </div>
              </li>
            ))}
          </ul>
        </Card>
      </div>
    </div>
  );
}
