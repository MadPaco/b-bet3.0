// src/utility/api.ts
import axios from 'axios';
import { jwtDecode } from 'jwt-decode';

const BASE_URL = 'http://127.0.0.1:8000/api';
const TOKEN_REFRESH_THRESHOLD = 60; // 60 seconds

let isRefreshing = false;
let refreshSubscribers: ((token: string) => void)[] = [];

const api = axios.create({
  baseURL: BASE_URL,
});

const onRefreshed = (token: string) => {
  refreshSubscribers.forEach((callback) => callback(token));
  refreshSubscribers = [];
};

const addRefreshSubscriber = (callback: (token: string) => void) => {
  refreshSubscribers.push(callback);
};

api.interceptors.request.use(
  async (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      const decoded: any = jwtDecode(token);
      const current_time = Date.now().valueOf() / 1000;

      if (decoded.exp && decoded.exp < current_time + TOKEN_REFRESH_THRESHOLD) {
        if (!isRefreshing) {
          isRefreshing = true;
          try {
            await fetchNewToken();
            const newToken = localStorage.getItem('token');
            if (newToken) {
              onRefreshed(newToken);
            }
          } catch (error) {
            console.error('Token refresh failed:', error);
            localStorage.removeItem('token');
            localStorage.removeItem('refresh_token');
            throw error;
          } finally {
            isRefreshing = false;
          }
        }

        return new Promise((resolve) => {
          addRefreshSubscriber((newToken) => {
            if (config.headers) {
              config.headers.Authorization = `Bearer ${newToken}`;
            }
            resolve(config);
          });
        });
      } else {
        if (config.headers) {
          config.headers.Authorization = `Bearer ${token}`;
        }
      }
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      try {
        await fetchNewToken();
        const newToken = localStorage.getItem('token');
        if (newToken) {
          originalRequest.headers.Authorization = `Bearer ${newToken}`;
          return api(originalRequest);
        }
      } catch (err) {
        console.error('Token refresh failed:', err);
      }
    }
    return Promise.reject(error);
  }
);

export async function fetchTeamInfo(team: string) {
  const response = await api.get(`/team/fetchTeaminfo/?favTeam=${team}`);
  if (!response.status === 200) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return response.data;
}

export async function fetchAllTeamLogos() {
  const response = await api.get('/team/fetchAllLogos');
  if (!response.status === 200) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return response.data;
}

export async function fetchPrimaryColor(team: string | null): Promise<string> {
  if (!team) {
    return 'gray';
  }
  try {
    const response = await api.get(`/team/fetchTeaminfo/?favTeam=${team}`);
    if (!response.status === 200) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.data.primaryColor;
  } catch (error) {
    console.error(error);
    return 'gray';
  }
}

export async function fetchTeamStats(team: string | null): Promise<string> {
  if (!team) {
    return 'team parameter missing';
  }
  try {
    const response = await api.get(`/team/fetchTeamStats/${team}/`);
    if (!response.status === 200) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.data;
  } catch (error) {
    console.error(error);
    return 'Error fetching team stats';
  }
}

export async function fetchDivisionStandings(conference: string, division: string) {
  const response = await api.get(`/team/fetchDivisionStandings/${conference}/${division}/`);
  if (!response.status === 200) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return response.data;
}

export async function fetchUserInfo(username: string) {
  try {
    const response = await api.get(`/user/fetchUser/?username=${username}`);
    if (!response.status === 200) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.data;
  } catch (error) {
    console.error('Failed to fetch user info:', error);
    return null;
  }
}

export async function fetchAllUsers() {
  try {
    const response = await api.get('/user/fetchAll');
    if (!response.status === 200) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.data;
  } catch (error) {
    console.error('Failed to fetch all users:', error);
    return null;
  }
}

export async function fetchAllTeamNames() {
  try {
    const response = await api.get('/team/fetchAllTeamNames');
    if (!response.status === 200) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.data;
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

  const response = await fetch(`${BASE_URL}/token/refresh`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `refresh_token=${refreshToken}`,
  });
  if (!response.ok) {
    throw new Error('Network response was not ok');
  }
  const data = await response.json();
  localStorage.setItem('token', data.token);
}

export async function updateUser(initialUsername: string, formData: FormData) {
  const response = await api.post(`/user/editUser?username=${initialUsername}`, formData);
  if (!response.status === 200) {
    const message = `An error has occurred: ${response.status}`;
    throw new Error(message);
  }
}

export async function fetchSchedule(weekNumber: number) {
  const response = await api.get(`/game/fetchWeek?weekNumber=${weekNumber}`);
  return response.data;
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
  const response = await api.post(`/game/editGame?gameID=${id}`, postBody);
  if (!response.status === 200) {
    const message = `An error has occurred: ${response.status}`;
    throw new Error(message);
  }
}

export async function addGame(postBody: GameBody) {
  const response = await api.post('/game/addGame', postBody);
  return response.data;
}

export async function deleteGame(id: number) {
  const response = await api.post(`/game/deleteGame?gameID=${id}`);
  return response.data;
}

interface BetBody {
  gameID: number;
  homePrediction: number | null;
  awayPrediction: number | null;
}

export async function addBets(postBody: BetBody[]) {
  const response = await api.post('/bet/addBets', postBody);
  return response.data;
}

export async function registerUser(email: string, username: string, password: string, favTeam: string) {
  const response = await api.post('/register', {
    email,
    username,
    password,
    favTeam,
  });
  return response.data;
}

export async function fetchBets(weekNumber?: number | null, username?: string | null) {
  let query = '';
  if (weekNumber) {
    query += `?weekNumber=${weekNumber}`;
  }
  if (username) {
    if (query.length > 0) query += '&';
    query += `user=${username}`;
  }

  const response = await api.get(`/bet/fetchBets${query}`);
  return response.data;
}

export async function fetchResults(weekNumber: number) {
  const response = await api.get(`/game/fetchResults?weekNumber=${weekNumber}`);
  return response.data;
}

export async function submitResults(scores: {
  [gameId: number]: { homeTeamScore: number; awayTeamScore: number };
}) {
  const response = await api.post('/game/submitResults', scores);
  return response.data;
}

export async function fetchUserStats(username: string) {
  const response = await api.get(`/stats/userStats/${username}`);
  return response.data;
}

export async function fetchShortUserStats(username: string) {
  const response = await api.get(`/stats/userStats/${username}/short`);
  return response.data;
}

export async function fetchLeadboard() {
  const response = await api.get('/stats/leaderboard');
  return response.data;
}

export async function fetchAllAchievements(username: string) {
  const response = await api.get(`/achievements/${username}/fetchNonHidden`);
  return response.data;
}

export async function fetchHiddenAchievements(username: string) {
  const response = await api.get(`/achievements/${username}/fetchHidden`);
  return response.data;
}

export async function fetchHiddenCompletion(username: string) {
  const response = await api.get(`/userAchievement/${username}/fetchHiddenCompletion`);
  return response.data;
}

export async function fetchNonHiddenCompletion(username: string) {
  const response = await api.get(`/userAchievement/${username}/fetchNonHiddenCompletion`);
  return response.data;
}

export async function fetchThreeLatestUserAchievement(username: string) {
  const response = await api.get(`/userAchievement/${username}/fetchThreeLatest`);
  return response.data;
}

export async function fetchUpcomingGames() {
  const response = await api.get('/schedule/upcomingGames');
  return response.data;
}

export async function fetchFavTeamBanner(username: string) {
  const response = await api.get(`/user/${username}/fetchFavTeamBanner`);
  return response.data;
}

export async function addPreseasonPrediction(username: string, data: any) {
  const response = await api.post(`/preseasonPrediction/${username}/add`, data);
  return response.data;
}

export async function fetchPreseasonPrediction(username: string) {
  const response = await api.get(`/preseasonPrediction/${username}/fetch`);
  return response.data;
}

export async function getCurrentWeek() {
  const response = await api.get('/schedule/getCurrentWeek');
  return response.data;
}
