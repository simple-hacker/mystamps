/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue';

window.Vue = Vue;

// Vue-js-modal
import VModal from 'vue-js-modal';

Vue.use(VModal, {
    dialog: true,
    injectModalsContainer: true
});

// vue-currency-input
import VueCurrencyInput from 'vue-currency-input'

const pluginOptions = {
    /* see config reference */
    globalOptions: {
        currency: 'GBP',
        locale: 'en-GB',
        precision: 2,
        allowNegative: false,
        distractionFree: false,
    }
}

Vue.use(VueCurrencyInput, pluginOptions)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('dropdown-menu', require('./components/DropdownMenu.vue').default);
Vue.component('login-modal', require('./components/LoginModal.vue').default);
Vue.component('register-modal', require('./components/RegisterModal.vue').default);
Vue.component('search-bar', require('./components/SearchBar.vue').default);
Vue.component('forgotten-password-modal', require('./components/ForgottenPasswordModal.vue').default);
Vue.component('collection-page', require('./components/CollectionPage.vue').default);
Vue.component('catalogue-page', require('./components/CataloguePage.vue').default);
Vue.component('collection-modal', require('./components/CollectionModal.vue').default);
Vue.component('gradings', require('./components/Gradings.vue').default);
Vue.component('denominations', require('./components/Denominations.vue').default);
Vue.component('issue-categories', require('./components/IssueCategories.vue').default);
Vue.component('monarchs', require('./components/Monarchs.vue').default);
Vue.component('browse-year', require('./components/BrowseYear.vue').default);
Vue.component('stamp-edit-page', require('./components/StampEditPage.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    methods: {
        confirmDeleteStamp(stamp) {
            this.$modal.show('dialog', {
                title: 'Delete Stamp?',
                text: `Are you sure you want to delete the stamp <strong>${stamp.title}</strong>?`,
                buttons: [
                  {
                    title: 'Cancel',
                    class: 'py-3 hover:bg-gray-200',
                    handler: () => {
                        this.$modal.hide('dialog');
                    }
                  },
                  {
                    title: 'Yes, delete.',
                    class: 'py-3 bg-red-500 hover:bg-red-600 text-white font-bold',
                    handler: () => {
                        axios.post('/stamp/'+stamp.id,{_method: 'delete'})
                            .then(response => {
                                this.$modal.hide('dialog');
                                location.reload();
                                // this.showMessage('Success', `Deleted stamp ${stamp.title}.`, true);
                            })
                            .catch(error => {
                                this.$modal.hide('dialog');
                                this.showMessage('Whoops, something went wrong!', error);
                            });
                    }
                  }
                ]
              })
        },
        confirmDeleteIssue(issue) {
          this.$modal.show('dialog', {
              title: 'Delete Issue?',
              text: `Are you sure you want to delete the issue <strong>${issue.title}</strong>?`,
              buttons: [
                {
                  title: 'Cancel',
                  class: 'py-3 hover:bg-gray-200',
                  handler: () => {
                      this.$modal.hide('dialog');
                  }
                },
                {
                  title: 'Yes, delete.',
                  class: 'py-3 bg-red-500 hover:bg-red-600 text-white font-bold',
                  handler: () => {
                      axios.post('/issue/'+issue.id,{_method: 'delete'})
                          .then(response => {
                              this.$modal.hide('dialog');
                              location.replace('/catalogue');
                              // this.showMessage('Success', `Deleted issue ${issue.title}.`, true, '/catalogue');
                          })
                          .catch(error => {
                              this.$modal.hide('dialog');
                              this.showMessage('Whoops, something went wrong!', error);
                          });
                  }
                }
              ]
            })
        },
        confirmImportIssue(issue) {
          this.$modal.show('dialog', {
            title: 'Reimport Issue?',
            text: `Are you sure you want to reimport this <strong>${issue.title}</strong>?  This will overwrite any changes you\'ve made.`,
            buttons: [
              {
                title: 'Cancel',
                class: 'py-3 hover:bg-gray-200',
                handler: () => {
                    this.$modal.hide('dialog');
                }
              },
              {
                title: 'Yes, Reimport issue.',
                class: 'py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold',
                handler: () => {
                    axios.get(`/scraper/issue/${issue.cgbs_issue}`)
                        .then(response => {
                            this.$modal.hide('dialog');
                            location.reload();
                        })
                        .catch(error => {
                            this.$modal.hide('dialog');
                            this.showMessage('Whoops, something went wrong!', error);
                        });
                }
              }
            ]
          });
        },
        showMessage(title, message, reload = false, redirect_location = null) {
          this.$modal.show('dialog', {
              title: title,
              text: message,
              buttons: [
                {
                  title: 'Okay',
                  class: 'py-3 hover:bg-gray-200',
                  handler: () => {
                      this.$modal.hide('dialog');
                      if (reload && redirect_location) {
                        return location.replace(redirect_location);
                      }
                      if (reload) {
                        return location.reload();
                      }
                  }
                }
              ]
            });
        }
    }
});
