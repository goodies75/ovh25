// Mock pour simuler l'upload en développement local
window.mockUpload = function(imageData, filename) {
  return new Promise((resolve) => {
    setTimeout(() => {
      // Simuler une réponse d'upload réussie avec une image de demo
      resolve({
        success: true,
        images: {
          medium: {
            url: 'https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=400&h=533&fit=crop',
            filename: 'demo_' + Date.now() + '.jpg',
            width: 400,
            height: 533
          }
        }
      });
    }, 1000);
  });
};
