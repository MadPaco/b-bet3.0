import React, { useState } from 'react';
import FormInput from './formComponents/FormInput';
import FormButton from './formComponents/FormButton';
import FormCard from './formComponents/FormCard';
import LoginHeaders from './formComponents/LoginHeaders';
import LoginFormLinks from './formComponents/LoginFormLinks';

const LoginPanel: React.FC = () => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

  const areInputsNotEmpty = () => {
    if (username === '' || password === '') return false;
    else return true;
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
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
    <FormCard>
      <form onSubmit={handleSubmit}>
        <LoginHeaders />
        <FormInput
          label="Username: "
          placeholder="Username"
          type="text"
          value={username}
          setValue={setUsername}
        />
        <FormInput
          label="Password: "
          placeholder="Password"
          type="password"
          value={password}
          setValue={setPassword}
          showPassword={showPassword}
          setShowPassword={setShowPassword}
        />
        <FormButton buttonText="Login" type="submit" />
        <LoginFormLinks />
        {errorMessage && <p className="text-red-500">{errorMessage}</p>}
      </form>
    </FormCard>
  );
};

export default LoginPanel;
