import { useState } from 'react';

//Icon imports
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons';

const LoginPanel: React.FC = () => {
  const [showPassword, setShowPassword] = useState(false);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  const [rememberMe, setRememberMe] = useState(false);

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
    <form
      className="p-8 bg-black rounded shadow-md w-full"
      onSubmit={handleSubmit}
    >
      <h2 className="text-2xl font-bold mb-8 text-teal-500 text-center">
        Login
      </h2>
      <input
        className="mb-4 w-full px-3 py-2 border border-gray-300 rounded"
        placeholder="enter username"
        onChange={(e) => setUsername(e.target.value)}
      />
      <div className="relative">
        <input
          className="w-full px-3 py-2 mb-4 border border-gray-300 rounded"
          placeholder="enter password"
          type={showPassword ? 'text' : 'password'}
          onChange={(e) => setPassword(e.target.value)}
        />
        <div
          className="absolute inset-y-0 right-0 pr-3 pb-4 flex items-center space-x-1 cursor-pointer"
          onClick={() => setShowPassword(!showPassword)}
        >
          <FontAwesomeIcon icon={showPassword ? faEyeSlash : faEye} />
          <div className="text-gray-400 text-sm">
            {showPassword ? 'Hide' : 'Show'}
          </div>
        </div>
      </div>
      <button
        className="mb-4 w-full px-3 py-2 bg-gray-500 text-white rounded"
        type="submit"
      >
        Sign in
      </button>
      {errorMessage && <p className="text-red-500">{errorMessage}</p>}
      <a className="text-blue-500 hover:underline" href="/forgot-password">
        Forgot Password?
      </a>
      <label className="flex items-center mt-4" htmlFor="rememberMeCheckbox">
        <input
          id="rememberMeCheckbox"
          type="checkbox"
          checked={rememberMe}
          onChange={(e) => setRememberMe(e.target.checked)}
        />
        <span className="ml-2">Remember me</span>
      </label>
    </form>
  );
};

export default LoginPanel;
