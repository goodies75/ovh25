
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import './App.css';
import Navigation from './components/Navigation/Navigation';
import HomePage from './components/pages/HomePage';
import AddPage from './components/pages/AddPage';
import ListPage from './components/pages/ListPage';
import AdminStatus from './components/security/AdminStatus';

function App() {
  return (
    <Router>
      <main>
        <div className="container">
          <Navigation />

          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/add" element={<AddPage />} />
            <Route path="/list" element={<ListPage />} />
          </Routes>

          <AdminStatus />
        </div>
      </main>
    </Router>
  );
}

export default App
