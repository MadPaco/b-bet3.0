import {
  fetchSchedule,
  updateGame,
  fetchAllTeamNames,
  deleteGame,
} from '../../utility/api';
import { useState, useEffect } from 'react';
import Modal from 'react-modal';

Modal.setAppElement('#root');

const GameEdit = () => {
  const [weekNumber, setWeekNumber] = useState(1);
  const [teamNames, setTeamNames] = useState<string[]>([]);
  const [schedule, setSchedule] = useState<Game[]>([]);
  const [modalIsOpen, setModalIsOpen] = useState(false);
  const [selectedGame, setSelectedGame] = useState<Game | null>(null);
  const [weekNumberInput, setWeekNumberInput] = useState<number | null>(null);
  const [dateInput, setDateInput] = useState<string | null>(null);
  const [locationInput, setLocationInput] = useState<string | null>(null);
  const [homeTeamInput, setHomeTeamInput] = useState<string | null>(null);
  const [awayTeamInput, setAwayTeamInput] = useState<string | null>(null);
  const [homeOddsInput, setHomeOddsInput] = useState<number | null>(null);
  const [awayOddsInput, setAwayOddsInput] = useState<number | null>(null);
  const [overUnderInput, setOverUnderInput] = useState<string | null>(null);
  const [editMode, setEditMode] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string>('');

  type Game = {
    id: number;
    weekNumber: number;
    date: string;
    location: string;
    homeTeam: string;
    awayTeam: string;
    homeTeamLogo: string;
    awayTeamLogo: string;
    homeOdds: number;
    awayOdds: number;
    overUnder: string;
  };

  const NFLWEEKS = 22;

  const getSchedule = async () => {
    const data = await fetchSchedule(weekNumber);
    setSchedule(data);
  };

  useEffect(() => {
    getSchedule();
  }, [weekNumber]);

  useEffect(() => {
    const fetchAllTeams = async () => {
      const teams = await fetchAllTeamNames();
      setTeamNames(teams);
    };

    fetchAllTeams();
  }, []);

  const toggleEditMode = () => {
    console.log('toggle');
    setEditMode(!editMode);
  };

  const openModal = (game: Game) => {
    //don't track the ID, it's not editable
    //to avoid interference with the db set ID
    setSelectedGame(game);
    setAwayTeamInput(game.awayTeam);
    setHomeTeamInput(game.homeTeam);
    setAwayOddsInput(game.awayOdds);
    setHomeOddsInput(game.homeOdds);
    setOverUnderInput(game.overUnder);
    setDateInput(game.date);
    setLocationInput(game.location);
    setWeekNumberInput(game.weekNumber);
    setModalIsOpen(true);
  };

  const closeModal = () => {
    setSelectedGame(null);
    setModalIsOpen(false);
    setEditMode(false);
  };

  const handleSubmission = async () => {
    if (!selectedGame) {
      return;
    }

    if (
      weekNumberInput === 0 ||
      weekNumberInput === null ||
      dateInput === '' ||
      dateInput === null ||
      locationInput === 'null' ||
      locationInput === null ||
      homeTeamInput === '' ||
      homeTeamInput === null ||
      awayTeamInput === '' ||
      awayTeamInput === null ||
      homeOddsInput === 0 ||
      homeOddsInput === null ||
      awayOddsInput === 0 ||
      awayOddsInput === null ||
      overUnderInput === '' ||
      overUnderInput === null
    ) {
      setErrorMessage('All fields must be filled out');
      return;
    }

    const postBody = {
      weekNumber: weekNumberInput,
      date: dateInput,
      location: locationInput,
      homeTeam: homeTeamInput,
      awayTeam: awayTeamInput,
      homeOdds: homeOddsInput,
      awayOdds: awayOddsInput,
      overUnder: parseFloat(overUnderInput),
    };

    await updateGame(selectedGame.id, postBody);
    getSchedule();
    setEditMode(false);
    setErrorMessage('');
  };

  const options = [];
  for (let i = 1; i <= NFLWEEKS; i++) {
    options.push(<option value={i}>Week {i}</option>);
  }

  return (
    <div className="w-full">
      <p className="text-red-500">This page is not meant to enter results</p>
      <select
        onChange={(e) => {
          setWeekNumber(Number((e.target as HTMLSelectElement).value));
        }}
        className="text-black"
      >
        {Array.from({ length: NFLWEEKS }, (_, i) => i + 1).map((number) => (
          <option key={number} value={number}>
            week {number}
          </option>
        ))}
      </select>
      {schedule.map((game) => (
        <div key={game.id}>
          <p>{game.date}</p>
          <p>
            {game.awayTeam} at {game.homeTeam}
          </p>
          <p>ID: {game.id}</p>
          <button onClick={() => openModal(game)}>Edit</button>
        </div>
      ))}
      {modalIsOpen && (
        <Modal
          isOpen={modalIsOpen}
          onRequestClose={closeModal}
          shouldCloseOnOverlayClick={true}
          style={{
            // the native modal doesn't work with tailwind..... so I have to do this
            // apologies to anybody seeing this
            overlay: {
              zIndex: 1000,
              backgroundColor: 'rgba(0, 0, 0, 0.5)',
            },
            content: {
              position: 'fixed',
              backgroundColor: 'gray',
              top: '50%',
              left: '50%',
              right: 'auto',
              bottom: 'auto',
              transform: 'translate(-50%, -50%)',
              width: '80%',
              height: '70%',
              overflow: 'auto',
              WebkitOverflowScrolling: 'touch',
              borderRadius: '4px',
              outline: 'none',
              padding: '20px',
            },
          }}
        >
          {selectedGame && (
            <div>
              <h2>
                {awayTeamInput} at {homeTeamInput}
              </h2>
              <p>Game ID: {selectedGame.id}</p>
              <div>
                <label>
                  {' '}
                  Week:
                  <input
                    className="w-3/4"
                    readOnly={!editMode}
                    value={weekNumberInput || ''}
                    onChange={(e) => {
                      setWeekNumberInput(Number(e.target.value));
                    }}
                  ></input>
                </label>
              </div>
              <div>
                <label>
                  {' '}
                  Date:
                  <input
                    className="w-3/4"
                    value={dateInput || ''}
                    readOnly={!editMode}
                    onChange={(e) => {
                      setDateInput(e.target.value);
                    }}
                  ></input>
                </label>
              </div>
              <div>
                <label>
                  {' '}
                  location:
                  <input
                    className="w-3/4"
                    value={locationInput || ''}
                    readOnly={!editMode}
                    onChange={(e) => {
                      setLocationInput(e.target.value);
                    }}
                  ></input>
                </label>
              </div>
              <div>
                <label>
                  awayTeam:
                  <select
                    onChange={(e) => {
                      setAwayTeamInput(e.target.value);
                    }}
                    disabled={!editMode}
                    className="w-3/4"
                    value={awayTeamInput || ''}
                  >
                    {teamNames.map((teamName) => (
                      <option key={teamName} value={teamName}>
                        {teamName}
                      </option>
                    ))}
                  </select>
                </label>
              </div>
              <div>
                <label>
                  homeTeam:
                  <select
                    onChange={(e) => {
                      setHomeTeamInput(e.target.value);
                    }}
                    disabled={!editMode}
                    className="w-3/4"
                    value={homeTeamInput || ''}
                  >
                    {teamNames.map((teamName) => (
                      <option key={teamName} value={teamName}>
                        {teamName}
                      </option>
                    ))}
                  </select>
                </label>
              </div>
              <div>
                <label>
                  {' '}
                  awayOdds:
                  <input
                    readOnly={!editMode}
                    onChange={(e) => {
                      setAwayOddsInput(Number(e.target.value));
                    }}
                    className="w-3/4"
                    value={awayOddsInput || ''}
                  ></input>
                </label>
              </div>
              <div>
                <label>
                  {' '}
                  homeOdds:
                  <input
                    readOnly={!editMode}
                    onChange={(e) => {
                      setHomeOddsInput(Number(e.target.value));
                    }}
                    className="w-3/4"
                    value={homeOddsInput || ''}
                  ></input>
                </label>
              </div>
              <div>
                <label>
                  {' '}
                  overUnder:
                  <input
                    readOnly={!editMode}
                    type="text"
                    onChange={(e) => {
                      setOverUnderInput(e.target.value);
                    }}
                    className="w-3/4"
                    value={overUnderInput || ''}
                  ></input>
                </label>
              </div>
            </div>
          )}
          <div className="flex justify-center items-center p-4">
            <button
              onClick={closeModal}
              className="px-4 py-2 bg-green-500 text-white rounded"
            >
              Close
            </button>
            <button
              onClick={() => {
                if (editMode) {
                  handleSubmission();
                } else {
                  toggleEditMode();
                }
              }}
              className="px-4 py-2 bg-blue-500 text-white rounded"
            >
              {editMode ? 'Save' : 'Edit'}
            </button>
            <button
              onClick={() => {
                if (selectedGame) {
                  deleteGame(selectedGame.id);
                  getSchedule();
                  closeModal();
                } else {
                  console.log('selectedGame is null');
                }
              }}
              className="px-4 py-2 bg-red-500 text-white rounded"
            >
              Delete
            </button>
            {errorMessage != '' && <p>{errorMessage}</p>}
          </div>
        </Modal>
      )}
    </div>
  );
};

export default GameEdit;
