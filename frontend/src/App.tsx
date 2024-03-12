import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import './index.css';
import Layout from './components/Layout';
import LoginPage from './pages/LoginPage';
import IndexPage from './pages/IndexPage';

const App: React.FC = () => {
  return (
    <Router>
      <Layout
        content={
          <Routes>
            <Route path="/login" element={<LoginPage />} />
            <Route path="/" element={<IndexPage />} />
          </Routes>
        }
      ></Layout>
    </Router>
  );
};

export default App;
