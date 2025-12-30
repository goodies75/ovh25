import { Link } from 'react-router-dom';
import { Card } from '../ui';

interface HomePageProps {
  statsData?: {
    totalComics: number;
    lastAdded?: string;
  };
}

export default function HomePage({ statsData }: HomePageProps) {
  return (
    <div className="min-h-screen py-12 px-4 sm:px-6 lg:px-8 animate-fade-in">
      {/* Hero Section */}
      <div className="max-w-7xl mx-auto mb-16">
        <div className="text-center mb-12">
          <h1 className="text-5xl sm:text-6xl lg:text-7xl font-display font-bold mb-6 bg-gradient-to-r from-primary-600 via-primary-500 to-teal-500 bg-clip-text text-transparent animate-gradient bg-200%">
            🎨 Opet Comics
          </h1>
          <p className="text-xl sm:text-2xl text-dark-600 max-w-3xl mx-auto leading-relaxed">
            Gérez votre collection de bandes dessinées et comics avec style.
            <br />
            <span className="text-lg text-dark-500">Ajoutez, organisez et consultez vos œuvres préférées en toute simplicité.</span>
          </p>
        </div>

        {/* Quick Actions */}
        <div className="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto mb-16">
          {/* Ajouter un Comic */}
          <Card
            glass
            className="p-8 group overflow-hidden relative"
          >
            {/* Effet de glow animé */}
            <div className="absolute -inset-0.5 bg-gradient-to-r from-primary-500 to-teal-500 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

            <div className="relative z-10">
              <div className="text-6xl mb-6 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                ➕
              </div>
              <h3 className="text-2xl font-display font-bold text-dark-800 mb-4">
                Ajouter un Comic
              </h3>
              <p className="text-dark-600 mb-6 leading-relaxed">
                Enrichissez votre collection en ajoutant de nouveaux comics avec toutes leurs informations détaillées.
              </p>
              <Link to="/add">
                <button className="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95 transition-all duration-300">
                  Ajouter maintenant →
                </button>
              </Link>
            </div>
          </Card>

          {/* Consulter la Liste */}
          <Card
            glass
            className="p-8 group overflow-hidden relative"
          >
            {/* Effet de glow animé */}
            <div className="absolute -inset-0.5 bg-gradient-to-r from-teal-500 to-primary-500 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>

            <div className="relative z-10">
              <div className="text-6xl mb-6 transform group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                📚
              </div>
              <h3 className="text-2xl font-display font-bold text-dark-800 mb-4">
                Consulter la Liste
              </h3>
              <p className="text-dark-600 mb-6 leading-relaxed">
                Parcourez votre collection, triez et gérez vos comics existants avec facilité.
              </p>
              <Link to="/list">
                <button className="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95 transition-all duration-300">
                  Voir la collection →
                </button>
              </Link>
            </div>
          </Card>
        </div>

        {/* Statistiques */}
        {statsData && (
          <div className="max-w-3xl mx-auto mb-16 animate-slide-up">
            <Card gradient className="p-8">
              <h3 className="text-2xl font-display font-bold text-dark-800 mb-6 text-center">
                📊 Statistiques de votre collection
              </h3>
              <div className="grid md:grid-cols-2 gap-6">
                <div className="text-center p-6 bg-white/50 rounded-xl backdrop-blur-sm">
                  <div className="text-5xl font-bold bg-gradient-to-r from-primary-600 to-teal-600 bg-clip-text text-transparent mb-2">
                    {statsData.totalComics}
                  </div>
                  <div className="text-dark-600 font-medium">Comics au total</div>
                </div>
                {statsData.lastAdded && (
                  <div className="text-center p-6 bg-white/50 rounded-xl backdrop-blur-sm">
                    <div className="text-lg font-semibold text-dark-700 mb-2">
                      {statsData.lastAdded}
                    </div>
                    <div className="text-dark-600 font-medium">Dernier ajout</div>
                  </div>
                )}
              </div>
            </Card>
          </div>
        )}

        {/* Fonctionnalités */}
        <div className="max-w-6xl mx-auto">
          <h3 className="text-3xl font-display font-bold text-center mb-12 text-dark-800">
            ✨ Fonctionnalités
          </h3>
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {[
              {
                icon: '🎨',
                title: 'Interface moderne',
                description: 'Design épuré et responsive pour tous vos appareils',
                gradient: 'from-purple-500 to-pink-500'
              },
              {
                icon: '🔍',
                title: 'Tri intelligent',
                description: 'Organisez votre collection selon vos préférences',
                gradient: 'from-blue-500 to-cyan-500'
              },
              {
                icon: '📱',
                title: 'Responsive',
                description: 'Utilisable sur mobile, tablette et ordinateur',
                gradient: 'from-green-500 to-teal-500'
              },
              {
                icon: '💾',
                title: 'Sauvegarde auto',
                description: 'Vos données sont automatiquement sauvegardées',
                gradient: 'from-orange-500 to-red-500'
              }
            ].map((feature, index) => (
              <Card
                key={index}
                hover
                className="p-6 group text-center transform transition-all duration-300 hover:-translate-y-2"
              >
                <div className={`text-5xl mb-4 transform group-hover:scale-110 transition-transform duration-300`}>
                  {feature.icon}
                </div>
                <h4 className="text-lg font-display font-semibold text-dark-800 mb-3">
                  {feature.title}
                </h4>
                <p className="text-dark-600 text-sm leading-relaxed">
                  {feature.description}
                </p>
                {/* Barre de couleur au bas */}
                <div className={`mt-4 h-1 w-0 group-hover:w-full transition-all duration-500 bg-gradient-to-r ${feature.gradient} rounded-full mx-auto`}></div>
              </Card>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
