import { useState, useEffect } from 'react';
import FormCard from '../components/formComponents/FormCard';
import FormInput from '../components/formComponents/FormInput';
import FormButton from '../components/formComponents/FormButton';
import RegisterHeaders from '../components/formComponents/RegisterHeaders';

const RegisterPage: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [response, setResponse] = useState(null);

  const handleRegister = async () => {
    console.log('Button clicked');
    try {
      const res = await fetch('http://127.0.0.1:8000/backend/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
      });
      const data = await res.json();
      setResponse(data);
    } catch (error) {
      console.error('Error:', error);
    }
  };

  useEffect(() => {
    if (response) {
      // TODO: Handle the response from the backend
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
