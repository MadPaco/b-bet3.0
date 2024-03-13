import React, { useState } from 'react';
import LoginPanel from '../components/LoginPanel';

const LoginPage: React.FC = () => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [errorMessage, setErrorMessage] = useState('');

  const areInputsNotEmpty = () => {
    if (username === '' || password === '') return false;
    else return true;
  };

  const handleLogin = async () => {
    if (areInputsNotEmpty()) {
      const response = await fetch('http://127.0.0.1:8000/backend/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: username, password }),
      });

      if (response.ok) {
        const data = await response.json();
        console.log('Token:', data.token);
        // Here you can save the token to the local storage or state and redirect the user
      } else {
        setErrorMessage('Invalid credentials');
      }
    } else {
      setErrorMessage('Please provide a username and a password.');
    }
  };

  return (
    <LoginPanel
      username={username}
      setUsername={setUsername}
      password={password}
      setPassword={setPassword}
      errorMessage={errorMessage}
      handleLogin={handleLogin}
    />
  );
};

export default LoginPage;
