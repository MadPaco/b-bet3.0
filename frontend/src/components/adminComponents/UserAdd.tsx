import { useState } from 'react';
import FormCard from '../form/FormCard';
import FormInput from '../form/FormInput';
import FormButton from '../form/FormButton';
import nflTeams from '../../data/nflTeams';
import { registerUser } from '../../utility/api';

const UserAdd: React.FC = () => {
  const [email, setEmail] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [favTeam, setFavTeam] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  const [showPassword, setShowPassword] = useState(false);

  const toggleShowPassword = () => setShowPassword(!showPassword);

  const handleRegister = async () => {
    if (password !== confirmPassword) {
      setErrorMessage('Passwords do not match');
      return;
    }
    try {
      await registerUser(email, username, password, favTeam);
      alert('Registration successful');
    } catch (error) {
      console.error('Error:', error);
      setErrorMessage('Registration failed');
    }
  };

  return (
    <FormCard>
      <FormInput
        placeholder="Enter Email"
        label="Email:"
        type="email"
        value={email}
        setValue={setEmail}
      />
      <FormInput
        placeholder="Enter username"
        label="Username:"
        type="text"
        value={username}
        setValue={setUsername}
      />
      <label className="text-gray-400">Favorite Team:</label>
      <select
        className="mb-2 w-full"
        value={favTeam}
        onChange={(e) => setFavTeam(e.target.value)}
      >
        <option value="">Select a team</option>
        <option value="None">None</option>
        {nflTeams.map((team: string) => (
          <option key={team} value={team}>
            {team}
          </option>
        ))}
      </select>
      <FormInput
        label="Password:"
        placeholder="password"
        type="password"
        value={password}
        setValue={setPassword}
        showPassword={showPassword}
        setShowPassword={toggleShowPassword}
      />
      <FormInput
        label="Confirm Password:"
        placeholder="confirm password"
        type="password"
        value={confirmPassword}
        setValue={setConfirmPassword}
        showPassword={showPassword}
        setShowPassword={toggleShowPassword}
      />
      <FormButton
        buttonText="Register"
        type="button"
        onClick={handleRegister}
      />
      {errorMessage && <p className="text-red-500">{errorMessage}</p>}
    </FormCard>
  );
};

export default UserAdd;
