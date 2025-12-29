// Mock APIs pour développement local
if (window.location.hostname === 'localhost') {
  console.log('Mode développement: activation des mocks API');
  
  // Mock data
  let mockFiches = [
    {
      "id": 1704067200000,
      "nom_serie": "Spider-Man: Into the Spider-Verse",
      "description": "Une aventure révolutionnaire dans le multivers avec Miles Morales",
      "image_url": "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop",
      "created_at": "2024-01-01T00:00:00.000Z",
      "numero": "1",
      "annee": "2024",
      "editeur": "Marvel",
      "auteur_couverture": "Test Artist",
      "autres_auteurs": ["Stan Lee"],
      "etat": "Très bon",
      "isbn": "",
      "numero_edition": "",
      "titre_secondaire": ""
    },
    {
      "id": 1704153600000,
      "nom_serie": "Batman: The Dark Knight Returns",
      "description": "Le retour épique de Batman dans une Gotham dystopique",
      "image_url": "https://images.unsplash.com/photo-1543832923-44667a44c804?w=300&h=400&fit=crop",
      "created_at": "2024-01-02T00:00:00.000Z",
      "numero": "1",
      "annee": "2024",
      "editeur": "DC",
      "auteur_couverture": "Frank Miller",
      "autres_auteurs": [],
      "etat": "Bon",
      "isbn": "",
      "numero_edition": "",
      "titre_secondaire": ""
    }
  ];

  // Intercepter les requêtes fetch
  const originalFetch = window.fetch;
  window.fetch = function(url, options) {
    // Mock GET fiches
    if (url.includes('get-fiches.php')) {
      return Promise.resolve(new Response(JSON.stringify(mockFiches), {
        status: 200,
        headers: { 'Content-Type': 'application/json' }
      }));
    }
    
    // Mock POST fiche
    if (url.includes('post-fiche.php') && options?.method === 'POST') {
      const body = JSON.parse(options.body);
      const newFiche = {
        id: Date.now(),
        created_at: new Date().toISOString(),
        ...body
      };
      mockFiches.push(newFiche);
      return Promise.resolve(new Response(JSON.stringify(newFiche), {
        status: 200,
        headers: { 'Content-Type': 'application/json' }
      }));
    }
    
    // Pour toutes les autres requêtes, utiliser fetch normal
    return originalFetch(url, options);
  };
}
