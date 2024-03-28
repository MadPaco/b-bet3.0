import { fetchAllTeamNames, addGame } from '../../utility/api';
import { useState, useEffect } from 'react';

const GameAdd = () => {
  useEffect(() => {
    const getTeamNames = async () => {
      const teamNames = await fetchAllTeamNames();
      setTeamNames(teamNames);
    };

    getTeamNames();
  }, []);

  const [teamNames, setTeamNames] = useState<string[]>([]);
  const [awayTeamInput, setAwayTeamInput] = useState<string>('');
  const [homeTeamInput, setHomeTeamInput] = useState<string>('');
  const [dateInput, setDateInput] = useState<string>('');
  const [timeInput, setTimeInput] = useState<string>('');
  const [weekInput, setWeekInput] = useState<number>(0);
  const [locationInput, setLocationInput] = useState<string>('');
  const [awayOddsInput, setAwayOddsInput] = useState<number>(0);
  const [homeOddsInput, setHomeOddsInput] = useState<number>(0);
  const [overUnderInput, setOverUnderInput] = useState<number>(0);

  const handleSubmit = async () => {
    const combinedTime = dateInput + ' ' + timeInput + ':00';

    const postBody = {
      awayTeam: awayTeamInput,
      homeTeam: homeTeamInput,
      date: combinedTime,
      weekNumber: weekInput,
      location: locationInput,
      awayOdds: awayOddsInput,
      homeOdds: homeOddsInput,
      overUnder: overUnderInput,
    };
    console.log(postBody);
    addGame(postBody);
  };

  return (
    <div>
      <h1 className="text-white text-2xl">Add Game</h1>
      <form className="flex flex-col">
        <label className="text-white">Away Team</label>
        <select
          onChange={(e) => {
            setAwayTeamInput(e.target.value);
          }}
          className="w-3/4"
          value={awayTeamInput || ''}
        >
          {teamNames.map((teamName) => (
            <option key={teamName} value={teamName}>
              {teamName}
            </option>
          ))}
        </select>
        <label className="text-white">Home Team</label>
        <select
          onChange={(e) => {
            setHomeTeamInput(e.target.value);
          }}
          className="w-3/4"
          value={homeTeamInput || ''}
        >
          {teamNames.map((teamName) => (
            <option key={teamName} value={teamName}>
              {teamName}
            </option>
          ))}
        </select>
        <label className="text-white">Date</label>
        <input
          onChange={(e) => {
            setDateInput(e.target.value);
          }}
          type="date"
          className="bg-gray-800 text-white"
          placeholder="Date"
        />
        <label className="text-white">Time</label>
        <input
          onChange={(e) => {
            setTimeInput(e.target.value);
          }}
          type="time"
          className="bg-gray-800 text-white"
          placeholder="Time"
        />
        <label className="text-white">Week</label>
        <input
          onChange={(e) => {
            setWeekInput(Number(e.target.value));
          }}
          type="number"
          className="bg-gray-800 text-white"
          placeholder="Week"
        />
        <label className="text-white">location</label>
        <input
          onChange={(e) => {
            setLocationInput(e.target.value);
          }}
          type="text"
          className="bg-gray-800 text-white"
          placeholder="Location"
        />
        <label className="text-white">awayOdds</label>
        <input
          onChange={(e) => {
            setAwayOddsInput(Number(e.target.value));
          }}
          className="bg-gray-800 text-white"
          type="number"
        />
        <label className="text-white">homeOdds</label>
        <input
          onChange={(e) => {
            setHomeOddsInput(Number(e.target.value));
          }}
          className="bg-gray-800 text-white"
          type="number"
        />
        <label className="text-white">overUnder</label>
        <input
          onChange={(e) => {
            setOverUnderInput(Number(e.target.value));
          }}
          className="bg-gray-800 text-white"
          type="number"
        />
        <button
          onClick={(e) => {
            e.preventDefault();
            handleSubmit();
          }}
          className="bg-green-500 text-white"
        >
          Add Game
        </button>
      </form>
    </div>
  );
};

export default GameAdd;
