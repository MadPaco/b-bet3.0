import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../components/auth/AuthContext';

const IndexPage: React.FC = () => {
  const navigate = useNavigate();
  const username = useAuth();

  useEffect(() => {
    if (!username) {
      navigate('/login');
    } else {
      navigate('/dashboard');
    }
  }, [username, navigate]);

  return null;
};

export default IndexPage;
