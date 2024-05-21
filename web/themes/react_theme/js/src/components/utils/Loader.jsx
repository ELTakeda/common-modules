import React from 'react'
import Logo from "../../../../assets/images/ddt-logo.svg"
import "../../../../scss/components/_loader.scss"

const Loader = () => {
  return (
    <>
      <section className="spinner-loader">
        <img src={Logo} alt="ddt-logo" />
        <div className="spinner-loader__dots">
          <div className="spinner-loader__circle"></div>
          <div className="spinner-loader__circle"></div>
          <div className="spinner-loader__circle"></div>
        </div>
      </section>
    </>
  )
}

export default Loader;