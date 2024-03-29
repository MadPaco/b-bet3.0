import React, { useState } from 'react';
import LoginPanel from '../components/auth/LoginPanel';
import Layout from '../components/layout/Layout';

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
      const response = await fetch('http://127.0.0.1:8000/api/login_check', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username: username, password }),
      });

      if (response.ok) {
        const data = await response.json();
        console.log(data);
        localStorage.setItem('token', data.token);
        localStorage.setItem('refresh_token', data.refresh_token);
        //change to navigate later
        window.location.href = './dashboard';
      } else {
        setErrorMessage('Invalid credentials');
      }
    } else {
      setErrorMessage('Please provide a username and a password.');
    }
  };

  return (
    <Layout
      content={
        <div className="flex items-center align-middle pt-10">
          <LoginPanel
            username={username}
            setUsername={setUsername}
            password={password}
            setPassword={setPassword}
            errorMessage={errorMessage}
            handleLogin={handleLogin}
          />
        </div>
      }
    />
  );
};

export default LoginPage;
