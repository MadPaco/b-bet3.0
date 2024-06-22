import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Layout from '../components/layout/Layout';
import FormCard from '../components/form/FormCard';
import FormInput from '../components/form/FormInput';
import FormButton from '../components/form/FormButton';
import RegisterHeaders from '../components/form/RegisterHeaders';
import nflTeams from '../data/nflTeams';

const RegisterPage: React.FC = () => {
  const [email, setEmail] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [favTeam, setFavTeam] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [profilePicture, setProfilePicture] = useState<File | null>(null);
  const [errorMessage, setErrorMessage] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const navigate = useNavigate();

  const toggleShowPassword = () => setShowPassword(!showPassword);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      setProfilePicture(e.target.files[0]);
    }
  };

  const handleRegister = async () => {
    if (password !== confirmPassword) {
      setErrorMessage('Passwords do not match');
      return;
    }

    const formData = new FormData();
    formData.append('email', email);
    formData.append('username', username);
    formData.append('password', password);
    formData.append('favTeam', favTeam);
    if (profilePicture) {
      formData.append('profilePicture', profilePicture);
    }

    try {
      const res = await fetch('http://127.0.0.1:8000/api/register', {
        method: 'POST',
        body: formData,
      });

      if (res.ok) {
        alert('Registration successful, redirecting to login page');
        navigate('/dashboard');
      } else {
        setErrorMessage('Registration failed. Please try again.');
      }
    } catch (error) {
      console.error('Error:', error);
      setErrorMessage('An error occurred. Please try again.');
    }
  };

  return (
    <Layout
      content={
        <FormCard>
          <RegisterHeaders />
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
          <label className="text-gray-400 mb-2">Profile Picture (optional):</label>
          <input
            type="file"
            accept="image/*"
            onChange={handleFileChange}
            className="mb-4 w-full"
          />
          <FormButton
            buttonText="Register"
            type="button"
            onClick={handleRegister}
          />
          {errorMessage && <p className="text-red-500">{errorMessage}</p>}
        </FormCard>
      }
    />
  );
};

export default RegisterPage;
