import React from 'react';
import FormInput from '../form/FormInput';
import FormButton from '../form/FormButton';
import FormCard from '../form/FormCard';
import LoginHeaders from '../form/LoginHeaders';
import LoginFormLinks from '../form/LoginFormLinks';

interface LoginPanelProps {
  username: string;
  setUsername: (username: string) => void;
  password: string;
  setPassword: (password: string) => void;
  errorMessage: string;
  handleLogin: () => void;
}

const LoginPanel: React.FC<LoginPanelProps> = ({
  username,
  setUsername,
  password,
  setPassword,
  errorMessage,
  handleLogin,
}) => {
  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();
    handleLogin();
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
        />
        <FormButton buttonText="Login" type="submit" />
        <LoginFormLinks />
        {errorMessage && <p className="text-red-500">{errorMessage}</p>}
      </form>
    </FormCard>
  );
};

export default LoginPanel;
