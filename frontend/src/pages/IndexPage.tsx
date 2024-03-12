import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

const IndexPage: React.FC = () => {
  const navigate = useNavigate();
  const isLoggedIn = false; // Placeholder

  useEffect(() => {
    if (!isLoggedIn) {
      navigate('/login');
    } else {
      // If the user is logged in, redirect them to the dashboard
      // navigate('/dashboard');
    }
  }, [isLoggedIn, navigate]);

  return null;
};

export default IndexPage;
