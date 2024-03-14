import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import FormCard from '../components/formComponents/FormCard';
import FormInput from '../components/formComponents/FormInput';
import FormButton from '../components/formComponents/FormButton';
import RegisterHeaders from '../components/formComponents/RegisterHeaders';
import nflTeams from '../data/nflTeams';

const RegisterPage: React.FC = () => {
  const [email, setEmail] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [favTeam, setFavTeam] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [response, setResponse] = useState(null);
  const navigate = useNavigate();

  const handleRegister = async () => {
    try {
      const res = await fetch('http://127.0.0.1:8000/backend/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, username, password, favTeam }),
      });

      if (res.ok) {
        const data = await res.json();
        setResponse(data);
        localStorage.setItem('token', data.token);
        navigate('/dashboard');
      }
    } catch (error) {
      console.error('Error:', error);
    }
  };

  useEffect(() => {
    if (response) {
      console.log(response);
    }
  }, [response]);

  return (
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
      />
      <FormInput
        label="Confirm Password:"
        placeholder="confirm password"
        type="password"
        value={confirmPassword}
        setValue={setConfirmPassword}
      />
      <FormButton
        buttonText="Register"
        type="button"
        onClick={handleRegister}
      />
    </FormCard>
  );
};

export default RegisterPage;
