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
        <FormButton buttonText="Login" />
        <LoginFormLinks />
        {errorMessage && <p className="text-red-500">{errorMessage}</p>}
      </form>
    </FormCard>
  );
};

export default LoginPanel;
