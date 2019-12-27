module.exports = {
  theme: {
    extend: {
      colors: {
        'face' : '#303030',
        'MNH' : '#2b6cb0',
        'VLMM' : '#3182ce',
        'LMM' : '#63b3ed',
        'MM' : '#90cdf4',
        'VFU' : '#c05621',
        'FU' : '#dd6b20',
        'GU' : '#ed8936',
        'AU' : '#f6ad55',
        'dark': 'var(--dark-color)',
        'darker': 'var(--darker-color)',
        'highlight': 'var(--highlight-color)',
        'light' : 'var(--light-color)'
      },
      minWidth: {
        '1/8' : '12.5%',
        '1/7' : '14.28%',
        '1/6' : '16.66%',
        '1/5' : '20%',
        '1/4' : '25%',
        '1/3' : '33.33%',
      },
      maxWidth: {
        '1/8' : '12.5%',
        '1/6' : '16.66%',
        '1/5' : '20%',
        '1/4' : '25%',
        '1/3' : '33.33%',
      },
    }, 
    inset: {
      '0': 0,
      auto: 'auto',
      '5': '0.5rem',
      '40': '2.5rem',
    },
    zIndex: {
      '0': 0,
      '10': 10,
      '20': 20,
      '25': 25,
      '30': 30,
      '40': 40,
      '50': 50,
      '75': 75,
      '99': 99,
      'auto': 'auto',
    },
    maxHeight: {
      '0': '0',
      '1/4': '25%',
      '1/2': '50%',
      '3/4': '75%',
      'full': '100%',
      'browse' : '81vh',
      'screen' : '100vh',
    }
  },
  variants: {},
  plugins: []
}
