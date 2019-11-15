import React, { Component } from 'react'
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import axios from 'axios'
import Select from 'react-select'

export default class MSelectKualifikasi extends Component {
  constructor(props){
    super(props)

    this.state = {
      data: []
    }
  }

  componentDidMount(){
    axios.get(`/api/kualifikasi`).then(response => {
      console.log(response)

      let data = []

      response.data.map((d) => {
        data.push({
          value: d.id,
          label: this.props.tipe_profesi == 1 ? d.deskripsi_ahli : d.deskripsi_trampil
        })
      })

      this.setState({
        data: data,
        loading: false
      })
    }).catch(err => {
      console.log(err.response)

      this.setState({
        loading: false,
      })
    })
  }

  render() {
    return (
      <Form.Group>
        <Form.Label>Kualifikasi</Form.Label>
        <Select placeholder="-- pilih kualifikasi --" options={this.state.data} onChange={(val) => this.props.onChange(val)}/>
      </Form.Group>
    )
  }
}
