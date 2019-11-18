import React, { Component } from 'react'
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import axios from 'axios'
import Select from 'react-select'

export default class MSelectKabupaten extends Component {
  constructor(props){
    super(props)

    this.state = {
      data: [],
      loading: false,
    }
  }

  componentDidMount() {
    this.props.onRef(this)
    
    this.getKabupaten(this.props.provinsiId)
  }

  componentWillUnmount() {
    this.props.onRef(undefined)
  }

  getKabupaten(provinsi_id){
    if(!this.state.loading){
      this.setState({data: [], loading: true})
      axios.get('/api/kabupaten/' + provinsi_id).then(response => {
        console.log(response)
  
        let data = []
  
        response.data.map((d) => {
          data.push({
            value: d.id,
            label: d.nama
          })
        })
  
        this.setState({
          data: data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)
  
        this.setState({
          loading: false
        })
      })
    }
  }

  render() {
    return (
      <Form.Group>
        <Form.Label>Kabupaten</Form.Label>
        <Select placeholder="-- pilih kabupaten --" value={this.state.data.filter(obj => {return obj.value == this.props.value})[0]} options={this.state.data} isLoading={this.state.loading} onChange={(val) => this.props.onChange(val)}/>
      </Form.Group>
    )
  }
}
