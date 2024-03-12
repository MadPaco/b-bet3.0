import React, { useState } from 'react';
import FormInput from './FormInput';
import FormButton from './FormButton';
import FormCard from './FormCard';
import LoginHeaders from './LoginHeaders';
import LoginFormLinks from './LoginFormLinks';

const LoginPanel: React.FC = () => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

  const areInputsNotEmpty = () => {
    if (username === '' || password === '') return false;
    else return true;
  };

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();
    if (areInputsNotEmpty()) {
      //successfull login
      //TODO implement submit with backend
    } else {
      setErrorMessage('Please provide a username and a password.');
    }
  };

  return (
    <FormCard>
      <form onSubmit={handleSubmit}>
        <LoginHeaders />
        <FormInput
          placeholder="Username"
          type="text"
          value={username}
          setValue={setUsername}
        />
        <FormInput
          placeholder="Password"
          type="password"
          value={password}
          setValue={setPassword}
          showPassword={showPassword}
          setShowPassword={setShowPassword}
        />
        <FormButton buttonText="Login" />
        <LoginFormLinks />
        {errorMessage && <p className="text-red-500">{errorMessage}</p>}
      </form>
    </FormCard>
  );
};

export default LoginPanel;
