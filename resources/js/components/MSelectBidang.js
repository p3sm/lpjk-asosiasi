import React, { Component } from 'react'
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import axios from 'axios'
import Select from 'react-select'

export default class MSelectBidang extends Component {
  constructor(props){
    super(props)

    this.state = {
      data: []
    }
  }

  componentDidMount(){
    axios.get(`/api/bidang/` + this.props.tipe_profesi).then(response => {
      console.log(response)

      let data = []

      response.data.map((d) => {
        data.push({
          value: d.id_bidang,
          label: d.id_bidang + " - " + d.deskripsi
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
        <Form.Label>Bidang</Form.Label>
        <Select placeholder="-- pilih bidang --" options={this.state.data} onChange={(val) => this.props.onChange(val)}/>
      </Form.Group>
    )
  }
}
