import AboutPage from "../pages/AboutPage.vue";
import LoginPage from "../pages/LoginPage.vue";
import RegisterPage from "../pages/RegisterPage.vue";
import ForgotpasswordPage from "../pages/ForgotpasswordPage.vue";
import ResetpasswordPage from "../pages/ResetpasswordPage.vue";
import PrivacyPage from "../pages/PrivacyPage.vue";
import NotFoundErrorPage from "../pages/errors/NotFoundErrorPage.vue";
import HomePage from "../pages/HomePage.vue";
import EventsPage from "../pages/EventsPage.vue";
import EventsCalendarPage from "../pages/EventsCalendarPage.vue";
import NeweventPage from "../pages/NeweventPage.vue";
import ImporteventPage from "../pages/ImporteventPage.vue";

const routes = [
  {
    path: "/",
    component: HomePage,
    name: "home",
  },
  {
    path: "/about",
    component: AboutPage,
    name: "about",
  },
  {
    path: "/privacy",
    component: PrivacyPage,
    name: "privacy",
  },
  {
    path: "/login",
    component: LoginPage,
    name: "login",
    meta: {
      guest: true,
    },
  },
  {
    path: "/forgot-password",
    component: ForgotpasswordPage,
    name: "forgot-password",
    meta: {
      guest: true,
    },
  },
  {
    path: "/reset-password/:token",
    component: ResetpasswordPage,
    name: "reset-password",
    meta: {
      guest: true,
    },
  },
  {
    path: "/register",
    component: RegisterPage,
    name: "register",
    meta: {
      guest: true,
    },
  },
  {
    path: "/events",
    component: EventsPage,
    name: "events",
    meta: {
      auth: true,
    },
  },
  {
    path: "/events-calendar",
    component: EventsCalendarPage,
    name: "events-calendar",
    meta: {
      auth: true,
    },
  },
  {
    path: "/addnewevent",
    component: NeweventPage,
    name: "addnewevent",
    meta: {
      auth: true,
    },
  },
  {
    path: "/importevent",
    component: ImporteventPage,
    name: "importevent",
    meta: {
      auth: true,
    },
  },
  {
    path: "/:notFound(.*)",
    name: "error.404",
    component: NotFoundErrorPage,
  },
];

export default routes;
