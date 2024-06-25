export async function fetchTeamInfo(team: string) {
  const token = localStorage.getItem('token');
  const response = await fetch(
    `http://127.0.0.1:8000/api/team/fetchTeaminfo/?favTeam=${team}`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    },
  );
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return await response.json();
}

//return the primary color of the team, or grey if no team is selected
export async function fetchPrimaryColor(team: string | null): Promise<string> {
  if (!team) {
    return 'gray';
  }

  const token = localStorage.getItem('token');
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/team/fetchTeaminfo/?favTeam=${team}`,
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      },
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    return data.primaryColor;
  } catch (error) {
    console.error(error);
    return 'gray';
  }
}

export async function fetchTeamStats(team: string | null): Promise<string> {
  if (!team) {
    return 'team parameter missing';
  }

  const token = localStorage.getItem('token');
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/team/fetchTeamStats/${team}/`,
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      },
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    return data;
  } catch (error) {
    console.error(error);
    return 'Error fetching team stats';
  }
}

export async function fetchDivisionStandings(conference: string, division: string) {
  const token = localStorage.getItem('token');
  const response = await fetch(
    `http://127.0.0.1:8000/api/team/fetchDivisionStandings/${conference}/${division}/`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    },
  );
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return await response.json();
}

export async function fetchUserInfo(username: string) {
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/user/fetchUser/?username=${username}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      },
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const userInfo = await response.json();
    return userInfo;
  } catch (error) {
    console.error('Failed to fetch user info:', error);
    return null;
  }
}

export async function fetchAllUsers() {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/user/fetchAll`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const users = await response.json();
    return users;
  } catch (error) {
    console.error('Failed to fetch all users:', error);
    return null;
  }
}

export async function fetchAllTeamNames() {
  try {
    const response = await fetch(
      'http://127.0.0.1:8000/api/team/fetchAllTeamNames',
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      },
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const teams = await response.json();
    return teams;
  } catch (error) {
    console.error('Failed to fetch all users:', error);
    return null;
  }
}

export async function fetchNewToken(): Promise<void> {
  const refreshToken = localStorage.getItem('refresh_token');
  if (!refreshToken) {
    throw new Error('No refresh token found');
  }

  return fetch('http://127.0.0.1:8000/api/token/refresh', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `refresh_token=${refreshToken}`,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then((data) => {
      localStorage.setItem('token', data.token);
    })
    .catch((error) => {
      console.error('There has been a problem with your fetch operation:', error);
      localStorage.removeItem('token');
      localStorage.removeItem('refresh_token');
      throw error;
    });
}
  

interface UserUpdateBody {
  username?: string;
  password?: string;
  email?: string;
  roles?: string[];
}

export async function updateUser(
  initialUsername: string,
  formData: FormData,
) {
  const response = await fetch(
    `http://127.0.0.1:8000/api/user/editUser?username=${initialUsername}`,
    {
      method: 'POST',
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('token'),
      },
      body: formData,
    },
  );

  if (!response.ok) {
    const message = `An error has occurred: ${response.status}`;
    throw new Error(message);
  }
}

export async function fetchSchedule(weekNumber: number) {
  const response = await fetch(
    `http://127.0.0.1:8000/api/game/fetchWeek?weekNumber=${weekNumber}`,
    {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    },
  );
  return response;
}

interface GameBody {
  weekNumber: number;
  date: string;
  location: string;
  homeTeam: string;
  awayTeam: string;
  homeOdds: number;
  awayOdds: number;
  overUnder: number;
}

export async function updateGame(id: number, postBody: GameBody) {
  const response = await fetch(
    `http://127.0.0.1:8000/api/game/editGame?gameID=${id}`,
    {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: 'Bearer ' + localStorage.getItem('token'),
      },
      body: JSON.stringify({
        ...postBody,
      }),
    },
  );

  if (!response.ok) {
    const message = `An error has occured: ${response.status}`;
    throw new Error(message);
  }
}

export async function addGame(postBody: GameBody) {
  const response = await fetch(`http://127.0.0.1:8000/api/game/addGame`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: 'Bearer ' + localStorage.getItem('token'),
    },
    body: JSON.stringify({
      ...postBody,
    }),
  });
  return response;
}

export async function deleteGame(id: number) {
  const response = await fetch(
    `http://127.0.0.1:8000/api/game/deleteGame?gameID=${id}`,
    {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    },
  );
  return response;
}

interface BetBody {
  gameID: number;
  homePrediction: number | null;
  awayPrediction: number | null;
}

export async function addBets(postBody: BetBody[]) {
  const response = await fetch(`http://127.0.0.1:8000/api/bet/addBets`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: 'Bearer ' + localStorage.getItem('token'),
    },
    body: JSON.stringify({
      ...postBody,
    }),
  });
  return response;
}

export async function fetchBets(
  weekNumber?: number | null,
  username?: string | null,
) {
  let query = '';
  if (weekNumber) {
    query += `?weekNumber=${weekNumber}`;
  }
  if (username) {
    if (query.length > 0) query += '&';
    query += `user=${username}`;
  }

  const response = await fetch(
    `http://127.0.0.1:8000/api/bet/fetchBets${query}`,
    {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    },
  );
  return response;
}

export async function fetchResults(weekNumber: number) {
  const response = await fetch(
    `http://127.0.0.1:8000/api/game/fetchResults?weekNumber=${weekNumber}`,
    {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    },
  );
  return response;
}

export async function submitResults(scores: {
  [gameId: number]: { homeTeamScore: number; awayTeamScore: number };
}) {
  const response = await fetch(`http://127.0.0.1:8000/api/game/submitResults`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: 'Bearer ' + localStorage.getItem('token'),
    },
    body: JSON.stringify({
      ...scores,
    }),
  });
  return response;
}

export async function fetchUserStats(username: string) {
  const response = await fetch(
    `http://127.0.0.1:8000/api/stats/${username}`,
    {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    },
  );
  return response;
}